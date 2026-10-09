#!/usr/bin/env python3
import argparse,csv,json,re,sys,collections,datetime
def main():
 ap=argparse.ArgumentParser(); ap.add_argument('csv'); ap.add_argument('--out'); ap.add_argument('--themes',default=None); a=ap.parse_args()
 tp=a.themes or __file__.replace('reviews.py','themes.json'); themes=json.load(open(tp,encoding='utf-8'))
 rows=[]
 with open(a.csv,newline='',encoding='utf-8-sig') as f:
  for r in csv.DictReader(f):
   if not (r.get('url') or '').strip(): continue
   rows.append(r)
 buckets={k:[] for k in themes}; unmatched=[]
 for r in rows:
  text=(r.get('text') or '').lower(); hit=False
  for name,keys in themes.items():
   if any(k.lower() in text for k in keys): buckets[name].append(r); hit=True
  if not hit: unmatched.append(r)
 lines=['# Feedback analysis','',f'Sample: {len(rows)} linked reviews; {len(set(r.get("source","") for r in rows))} sources.','']
 for name,rs in sorted(buckets.items(),key=lambda x:-len(x[1])):
  if not rs: continue
  src=len(set(r.get('source','') for r in rs)); thin=len(rs)<3 or src<2; lines += [f'## {name} — {len(rs)} reviews / {src} sources'+(' — thin evidence' if thin else '')]
  for r in rs[:5]: lines.append(f"- {r.get('text','').strip()} — {r.get('url','')}")
  lines.append('')
 if unmatched:
  lines += ['## Unmatched low/other feedback']+[f"- {r.get('text','').strip()} — {r.get('url','')}" for r in unmatched[:20]]
 out='\n'.join(lines)
 if a.out: open(a.out,'w',encoding='utf-8').write(out+'\n')
 else: print(out)
 return 0
if __name__=='__main__': sys.exit(main())
