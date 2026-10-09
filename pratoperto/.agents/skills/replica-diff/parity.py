#!/usr/bin/env python3
import argparse,csv,json,sys,glob
W={'must':3,'should':2,'could':1,'p0':3,'p1':2,'p2':1}; C={'yes':1,'done':1,'partial':.5,'no':0,'todo':0,'':0}
def load(p):
 with open(p,newline='',encoding='utf-8-sig') as f: return [dict((k.strip().lower(),(v or '').strip()) for k,v in r.items()) for r in csv.DictReader(f) if (r.get('feature') or '').strip()]
def calc(rows):
 count=[]; skip=[]; extra=[]
 for r in rows:
  if r.get('original','yes').lower() in ('no','false','n'): extra.append(r); continue
  if r.get('clone','').lower()=='skip': skip.append(r); continue
  w=W.get(r.get('priority','').lower(),1); c=C.get(r.get('clone','').lower(),0); count.append((r,w,c))
 total=sum(w for _,w,_ in count); got=sum(w*c for _,w,c in count); fs=100*got/total if total else 0
 missing=[r for r,w,c in sorted(count,key=lambda x:(-x[1],x[2],x[0].get('area',''))) if c<1]
 must=[x for x in count if x[1]==3]
 return {'feature_score':round(fs,1),'counted':len(count),'must_have_done':sum(c==1 for _,_,c in must),'must_have_total':len(must),'missing':missing,'skipped':skip,'extras':extra}
def main():
 ap=argparse.ArgumentParser(); ap.add_argument('matrix'); ap.add_argument('--visual',nargs='*',default=[]); ap.add_argument('--json',action='store_true'); ap.add_argument('--markdown',action='store_true'); ap.add_argument('--fail-under',type=float); a=ap.parse_args()
 try: r=calc(load(a.matrix)); vs=[]
 except Exception as e: print('parity:',e,file=sys.stderr); return 2
 for pat in a.visual:
  for p in glob.glob(pat):
   try: d=json.load(open(p,encoding='utf-8')); vs.append(float(d['score']))
   except Exception: pass
 r['layout_score']=round(sum(vs)/len(vs),1) if vs else None; r['overall']=round(.8*r['feature_score']+.2*r['layout_score'],1) if vs else r['feature_score']
 if a.json: print(json.dumps(r,indent=2))
 else:
  print(f"Parity: {r['overall']:.1f}/100\nfeatures {r['feature_score']:.1f}; must-haves {r['must_have_done']}/{r['must_have_total']}")
  if r['layout_score'] is not None: print('layout',r['layout_score'])
  if r['must_have_done']<r['must_have_total']: print('Not shippable: must-have features are missing.')
  print('\nMissing:')
  for m in r['missing']: print(f"- [{m.get('priority','could')}] {m.get('area','')}: {m.get('feature','')} ({m.get('clone','no') or 'no'})")
 if a.fail_under is not None and r['overall']<a.fail_under: return 1
 return 0
if __name__=='__main__': sys.exit(main())
