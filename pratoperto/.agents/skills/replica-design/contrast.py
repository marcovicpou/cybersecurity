#!/usr/bin/env python3
import argparse,json,re,sys
HEX=re.compile(r'^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$'); NEEDS={'normal':(4.5,7),'large':(3,4.5),'ui':(3,3)}
def ph(v):
 m=HEX.match(v.strip());
 if not m: raise ValueError('not a hex colour: %r'%v)
 h=m.group(1); h=''.join(c*2 for c in h) if len(h)==3 else h
 return tuple(int(h[i:i+2],16) for i in (0,2,4))
def lum(rgb):
 def c(x): x/=255.; return x/12.92 if x<=.04045 else ((x+.055)/1.055)**2.4
 r,g,b=(c(x) for x in rgb); return .2126*r+.7152*g+.0722*b
def ratio(a,b):
 x,y=lum(ph(a)),lum(ph(b)); hi,lo=max(x,y),min(x,y); return (hi+.05)/(lo+.05)
def flat(o,p=''):
 out={}
 if isinstance(o,dict):
  if isinstance(o.get('value'),str): return {p:o['value']}
  if isinstance(o.get('$value'),str): return {p:o['$value']}
  for k,v in o.items():
   if not k.startswith(('$','_')): out.update(flat(v,f'{p}-{k}' if p else k))
 elif isinstance(o,str) and HEX.match(o.strip()): out[p]=o.strip()
 return out
def main():
 ap=argparse.ArgumentParser(); ap.add_argument('args',nargs='+'); ap.add_argument('--json',action='store_true'); a=ap.parse_args()
 if len(a.args)==2 and all(HEX.match(x) for x in a.args): cols={'fg':a.args[0],'bg':a.args[1]}; pairs=[['fg','bg']]
 else:
  try: t=json.load(open(a.args[0],encoding='utf-8'))
  except Exception as e: print('contrast:',e,file=sys.stderr); return 2
  cols={k:v for k,v in flat(t.get('color',t.get('colors',{}))).items() if HEX.match(v)}
  pairs=t.get('pairs') or [[f,b] for f in cols if re.search(r'(^|-)(text|fg|on)(-|$)',f) for b in cols if re.search(r'(^|-)(bg|background|surface)(-|$)',b)]
 rows=[]
 for p in pairs:
  if len(p)<2: continue
  fg,bg=p[:2]; size=p[2] if len(p)>2 and p[2] in NEEDS else 'normal'
  if fg not in cols or bg not in cols: rows.append({'fg':fg,'bg':bg,'error':'unknown token','aa':False}); continue
  r=ratio(cols[fg],cols[bg]); aa,aaa=NEEDS[size]; rows.append({'fg':fg,'bg':bg,'size':size,'ratio':round(r,2),'aa':r>=aa,'aaa':r>=aaa})
 if a.json: print(json.dumps(rows,indent=2))
 else:
  for r in rows: print(('FAIL' if not r.get('aa') else 'PASS'),r.get('ratio','?'),r['fg'],'on',r['bg'])
  print(f"{len(rows)} pairs, {sum(not r.get('aa',False) for r in rows)} failing AA")
 return 1 if any(not r.get('aa',False) for r in rows) else 0
if __name__=='__main__': sys.exit(main())
