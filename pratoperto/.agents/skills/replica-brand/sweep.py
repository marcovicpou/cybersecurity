#!/usr/bin/env python3
import argparse,json,os,re,sys
SKIP={'.git','node_modules','.next','dist','build','coverage','replica','.venv','venv'}
def main():
 ap=argparse.ArgumentParser(); ap.add_argument('root',nargs='?',default='.'); ap.add_argument('--avoid',default=''); ap.add_argument('--domains',default=''); ap.add_argument('--colors',default=''); ap.add_argument('--config'); a=ap.parse_args(); terms=[]
 if a.config:
  c=json.load(open(a.config,encoding='utf-8')); terms += c.get('avoid',[])+c.get('domains',[])+c.get('colors',[])
 terms += [x.strip() for x in (a.avoid+','+a.domains+','+a.colors).split(',') if x.strip()]
 terms=list(dict.fromkeys(terms)); hits=[]
 for base,dirs,files in os.walk(a.root):
  dirs[:]=[d for d in dirs if d not in SKIP]
  for fn in files:
   p=os.path.join(base,fn); rel=os.path.relpath(p,a.root)
   for t in terms:
    if t.lower() in rel.lower(): hits.append((rel,'filename',t))
   try:
    data=open(p,'r',encoding='utf-8',errors='ignore').read()
   except OSError: continue
   for t in terms:
    for m in re.finditer(re.escape(t),data,re.I): hits.append((rel,data.count('\n',0,m.start())+1,t))
 if hits:
  for h in hits: print(f'{h[0]}:{h[1]}: {h[2]}')
  print(f'{len(hits)} leftover match(es)'); return 1
 print('clean: no configured target-brand leftovers found'); return 0
if __name__=='__main__': sys.exit(main())
