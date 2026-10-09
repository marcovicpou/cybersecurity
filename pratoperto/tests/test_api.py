"""Testes locais de contrato e regras; nunca consultam produtos externos."""
import http.cookiejar, json, os, unittest, urllib.request, urllib.error, uuid

BASE = os.environ.get('PRATOPERTO_TEST_URL', 'http://127.0.0.1:8090/').rstrip('/') + '/'
class Client:
    def __init__(self):
        self.jar = http.cookiejar.CookieJar()
        self.opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(self.jar))
        self.csrf = ''
        self.state = {}
        self.state = self.call('bootstrap')[1]
        self.csrf = self.state['csrf']
    def call(self, action, body=None, ident=None, csrf=None):
        url = BASE + 'api.php?action=' + action + ('&id='+ident if ident else '')
        headers = {'Content-Type':'application/json', 'X-CSRF-Token':self.csrf if csrf is None else csrf}
        req = urllib.request.Request(url, data=None if body is None else json.dumps(body).encode(), headers=headers)
        try:
            with self.opener.open(req, timeout=15) as response: status, data = response.status,json.load(response)
        except urllib.error.HTTPError as error: status, data = error.code,json.load(error)
        if data.get('cart') is not None: self.state.update(data)
        if data.get('state'): self.state.update(data['state'])
        if data.get('csrf'): self.csrf=data['csrf']
        return status,data
    def add(self, product=0, qty=1, option=0, **extra):
        return self.call('cart',dict(op='add',version=self.state['cart']['version'],product_id=self.state['products'][product]['id'],quantity=qty,option_index=option,note='',**extra))
    def address(self, **extra):
        return self.call('address',dict(dict(street='Rua fictícia 🥗',number='12-A',district='Jardim',city='São Teste',extra='Bloco Á'),**extra))
    def checkout(self, key=None, **extra):
        return self.call('checkout',dict(version=self.state['cart']['version'],payment='demo_pix',idempotency_key=key or str(uuid.uuid4()),**extra))
class API(unittest.TestCase):
    def setUp(self): self.a=Client(); self.b=Client()
    def test_catalog_seed_and_empty_state(self):
        s=self.a.state
        self.assertEqual(len(s['restaurants']),6); self.assertEqual(len(s['products']),24)
        self.assertEqual(s['cart']['items'],[]); self.assertEqual(s['cart']['total'],0)
        self.assertTrue(all(isinstance(p['price'],int) for p in s['products']))
    def test_csrf_method_and_invalid_body(self):
        self.assertEqual(self.a.call('favorite',{'restaurant_id':self.a.state['restaurants'][0]['id']},csrf='bad')[0],403)
        self.assertEqual(self.a.call('favorite')[0],405)
        self.assertEqual(self.a.call('nonexistent')[0],404)
        self.assertEqual(self.a.call('address',{'street':12})[0],422)
        self.assertEqual(self.a.address(street='')[0],422)
        self.assertEqual(self.a.address(street='x'*101)[0],422)
        self.assertEqual(self.a.address()[0],200)
    def test_cart_price_options_quantity_and_notes(self):
        self.assertEqual(self.a.add(option=1)[0],200)
        c=self.a.state['cart']; self.assertEqual(c['subtotal'],3190); self.assertEqual(c['total'],3780)
        self.assertEqual(self.a.add(qty=2,option=1)[0],200); self.assertEqual(self.a.state['cart']['count'],3)
        item=self.a.state['cart']['items'][0]
        self.assertEqual(self.a.call('cart',dict(op='set',version=self.a.state['cart']['version'],item_id=item['id'],quantity=2,unit_price=1,total=1))[0],200)
        self.assertEqual(self.a.state['cart']['total'],6970)
        self.assertEqual(self.a.add(qty=0)[0],422); self.assertEqual(self.a.add(qty=21)[0],422)
        self.assertEqual(self.a.add(qty='2')[0],422); self.assertEqual(self.a.add(option=99)[0],422)
        self.assertEqual(self.a.call('cart',dict(op='add',version=self.a.state['cart']['version'],product_id=item['product_id'],quantity=1,option_index=0,note='x'*181))[0],422)
    def test_unavailable_closed_and_replace_confirmation(self):
        self.assertEqual(self.a.add(product=2)[0],422)
        self.assertEqual(self.a.add(product=16)[0],422)
        self.a.add(); status,data=self.a.add(product=4)
        self.assertEqual(status,409);self.assertEqual(data['code'],'RESTAURANT_CONFLICT')
        self.assertEqual(self.a.state['cart']['count'],1)
        self.assertEqual(self.a.add(product=4,replace=True)[0],200)
        self.assertEqual(self.a.state['cart']['restaurant']['slug'],'forno-do-bairro')
        self.assertEqual(self.a.state['cart']['count'],1)
    def test_stale_cart_version_and_cross_user_item(self):
        old=self.a.state['cart']['version']; self.a.add(); item=self.a.state['cart']['items'][0]
        status,data=self.a.call('cart',dict(op='clear',version=old))
        self.assertEqual(status,409);self.assertEqual(data['state']['cart']['count'],1)
        self.assertEqual(self.b.call('cart',dict(op='remove',version=0,item_id=item['id']))[0],404)
        self.assertEqual(self.a.call('bootstrap')[1]['cart']['count'],1)
    def test_minimum_and_address_required(self):
        self.a.add(product=3)
        self.assertEqual(self.a.state['cart']['remaining_minimum'],1210)
        self.assertEqual(self.a.checkout()[0],422)
        self.a.add(); self.assertEqual(self.a.checkout()[0],422)
        self.a.address()
        self.assertEqual(self.a.checkout()[0],200)
    def test_coupon_rules_and_server_totals(self):
        self.assertEqual(self.a.call('coupon',dict(version=0,code='BEMVINDO10'))[0],422)
        self.a.add()
        self.assertEqual(self.a.call('coupon',dict(version=self.a.state['cart']['version'],code='UNKNOWN'))[0],422)
        self.assertEqual(self.a.call('coupon',dict(version=self.a.state['cart']['version'],code='bemvindo10'))[0],200)
        self.assertEqual(self.a.state['cart']['discount'],259)
        self.assertEqual(self.a.state['cart']['total'],2921)
        self.a.call('coupon',dict(version=self.a.state['cart']['version'],code='ENTREGAGRATIS'))
        self.assertEqual(self.a.state['cart']['total'],2590)
        self.a.call('coupon',dict(version=self.a.state['cart']['version'],code=''))
        self.assertEqual(self.a.state['cart']['discount'],0)
        self.a.call('cart',dict(op='clear',version=self.a.state['cart']['version']))
        self.a.add(qty=10);self.a.call('coupon',dict(version=self.a.state['cart']['version'],code='BEMVINDO10'))
        self.assertEqual(self.a.state['cart']['discount'],1500)
    def test_checkout_idempotency_persistence_isolation_and_cancel(self):
        self.a.address();self.a.add();key=str(uuid.uuid4())
        version=self.a.state['cart']['version']
        status,data=self.a.call('checkout',dict(version=version,payment='demo_card',idempotency_key=key,subtotal=1,total=1,fee=0))
        self.assertEqual(status,200);o=data['order'];self.assertEqual(o['total'],3180)
        self.assertEqual(self.a.state['cart']['count'],0)
        status,data=self.a.call('checkout',dict(version=version,payment='demo_card',idempotency_key=key))
        self.assertEqual(status,200);self.assertEqual(data['order']['id'],o['id'])
        self.assertEqual(len(self.a.call('orders')[1]['orders']),1)
        self.assertEqual(self.a.call('order',ident=o['id'])[1]['order']['total'],3180)
        self.assertEqual(self.b.call('order',ident=o['id'])[0],404)
        self.assertEqual(self.b.call('cancel',{'id':o['id']})[0],404)
        self.assertEqual(self.a.call('cancel',{'id':o['id']})[1]['order']['status'],'cancelled')
        self.assertEqual(self.a.call('cancel',{'id':o['id']})[0],200)
    def test_favorites_and_delete_current_visitor_only(self):
        r=self.a.state['restaurants'][0]['id']
        self.a.call('favorite',{'restaurant_id':r});self.assertEqual(self.a.state['favorites'],[r])
        self.assertEqual(self.b.state['favorites'],[])
        self.a.call('favorite',{'restaurant_id':r});self.assertEqual(self.a.state['favorites'],[])
        self.a.address();self.a.add();self.a.checkout()
        self.b.address();self.b.add()
        old_csrf=self.a.csrf;self.assertEqual(self.a.call('reset',{})[0],200)
        self.assertNotEqual(self.a.csrf,old_csrf)
        self.assertEqual(self.a.call('orders')[1]['orders'],[])
        self.assertEqual(self.a.state['address'],[])
        self.assertEqual(self.b.call('bootstrap')[1]['cart']['count'],1)
        self.assertEqual(self.a.call('favorite',{'restaurant_id':r},csrf=old_csrf)[0],403)
    def test_stale_checkout_rolls_back_and_can_retry(self):
        self.a.address();self.a.add()
        version=self.a.state['cart']['version']
        self.assertEqual(self.a.call('checkout',dict(version=version-1,payment='demo_pix',idempotency_key=str(uuid.uuid4())))[0],409)
        self.assertEqual(self.a.checkout()[0],200)
        self.assertEqual(len(self.a.call('orders')[1]['orders']),1)
if __name__=='__main__': unittest.main(verbosity=2)
