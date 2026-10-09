#!/usr/bin/env python3
import argparse,json,re,sys
LIMITS={'app_store':{'name':30,'subtitle':30,'promotional_text':170,'keywords':100,'description':4000},'google_play':{'title':30,'short_description':80,'full_description':4000}}
def main():
 ap=argparse.ArgumentParser(); ap.add_argument('file'); a=ap.parse_args(); d=json.load(open(a.file,encoding='utf-8')); avoid=[x.lower() for x in d.get('avoid',[])]; errs=[]; warns=[]
 for store,fields in LIMITS.items():
  obj=d.get(store,{})
  for k,limit in fields.items():
   v=str(obj.get(k,''));
   if len(v)>limit: errs.append(f'{store}.{k}: {len(v)}/{limit} characters')
   lv=v.lower()
   for bad in avoid:
    if bad and bad in lv: errs.append(f'{store}.{k}: contains avoided target name {bad!r}')
  short=' '.join(str(obj.get(x,'')) for x in ('name','subtitle','title','short_description')).lower()
  if re.search(r'\b(best|#1|number one|cheapest|lowest price)\b',short): warns.append(f'{store}: ranking/price claim in short metadata')
 print('\n'.join('ERROR '+e for e in errs) or 'listing: no blocking errors')
 for w in warns: print('WARN '+w)
 return 1 if errs else 0
if __name__=='__main__': sys.exit(main())
