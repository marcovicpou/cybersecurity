#!/usr/bin/env python3
"""Small standard-library PNG layout/pixel comparer for screenshots."""
import argparse,struct,zlib,json,sys,math
SIG=b'\x89PNG\r\n\x1a\n'
def paeth(a,b,c):
 p=a+b-c; q=[(abs(p-a),a),(abs(p-b),b),(abs(p-c),c)]; return min(q,key=lambda x:x[0])[1]
def read_png(path):
 d=open(path,'rb').read();
 if d[:8]!=SIG: raise ValueError('not PNG')
 pos=8; ih=None; ids=[]
 while pos+8<=len(d):
  n=struct.unpack('>I',d[pos:pos+4])[0]; t=d[pos+4:pos+8]; b=d[pos+8:pos+8+n]; pos+=12+n
  if t==b'IHDR': ih=struct.unpack('>IIBBBBB',b)
  elif t==b'IDAT': ids.append(b)
  elif t==b'IEND': break
 if not ih: raise ValueError('missing IHDR')
 w,h,depth,color,_,_,inter=ih
 if inter or depth!=8 or color not in (0,2,4,6): raise ValueError('requires non-interlaced 8-bit grayscale/RGB/RGBA PNG')
 ch={0:1,2:3,4:2,6:4}[color]; stride=w*ch; raw=zlib.decompress(b''.join(ids)); rows=[]; prev=bytearray(stride); p=0
 for y in range(h):
  f=raw[p]; cur=bytearray(raw[p+1:p+1+stride]); p+=1+stride
  for i in range(stride):
   l=cur[i-ch] if i>=ch else 0; u=prev[i]; ul=prev[i-ch] if i>=ch else 0
   add=0 if f==0 else l if f==1 else u if f==2 else (l+u)//2 if f==3 else paeth(l,u,ul) if f==4 else 0
   cur[i]=(cur[i]+add)&255
  row=[]
  for x in range(w):
   s=cur[x*ch:(x+1)*ch]
   if color==0: r=g=b=s[0]; a=255
   elif color==2: r,g,b=s; a=255
   elif color==4: r=g=b=s[0]; a=s[1]
   else: r,g,b,a=s
   inv=255-a; row.append(((r*a+255*inv)//255,(g*a+255*inv)//255,(b*a+255*inv)//255))
  rows.append(row); prev=cur
 return w,h,rows
def resize(img,nw):
 w,h,r=img; nh=max(1,round(h*nw/w)); return nw,nh,[[r[min(h-1,int(y*h/nh))][min(w-1,int(x*w/nw))] for x in range(nw)] for y in range(nh)]
def gray(p): return (299*p[0]+587*p[1]+114*p[2])//1000
def write_png(path,w,h,rows):
 raw=bytearray()
 for row in rows:
  raw.append(0)
  for p in row: raw.extend(p)
 def ch(t,b): return struct.pack('>I',len(b))+t+b+struct.pack('>I',zlib.crc32(t+b)&0xffffffff)
 open(path,'wb').write(SIG+ch(b'IHDR',struct.pack('>IIBBBBB',w,h,8,2,0,0,0))+ch(b'IDAT',zlib.compress(bytes(raw)))+ch(b'IEND',b''))
def main():
 ap=argparse.ArgumentParser(); ap.add_argument('original'); ap.add_argument('clone'); ap.add_argument('--out'); ap.add_argument('--json',action='store_true'); ap.add_argument('--mode',choices=['layout','pixel'],default='layout'); ap.add_argument('--width',type=int,default=480); ap.add_argument('--fail-under',type=float); a=ap.parse_args()
 try: A=read_png(a.original); B=read_png(a.clone)
 except Exception as e: print('imgdiff:',e,file=sys.stderr); return 2
 tw=min(a.width,A[0],B[0]); A=resize(A,tw); B=resize(B,tw); h=min(A[1],B[1]); dif=[]; score_acc=[]; cell=max(8,tw//12); out=[]
 for y in range(h):
  row=[]
  for x in range(tw): row.append(A[2][y][x])
  out.append(row)
 for y0 in range(0,h,cell):
  for x0 in range(0,tw,cell):
   vals=[]
   for y in range(y0,min(h,y0+cell)):
    for x in range(x0,min(tw,x0+cell)):
     if a.mode=='pixel': d=max(abs(A[2][y][x][i]-B[2][y][x][i]) for i in range(3))/255
     else:
      ax=gray(A[2][y][x]); bx=gray(B[2][y][x]); ar=gray(A[2][y][min(tw-1,x+1)]); br=gray(B[2][y][min(tw-1,x+1)]); ad=gray(A[2][min(h-1,y+1)][x]); bd=gray(B[2][min(h-1,y+1)][x]); d=min(1,abs((abs(ar-ax)+abs(ad-ax))-(abs(br-bx)+abs(bd-bx)))/100)
     vals.append(d)
   m=sum(vals)/len(vals) if vals else 0; score_acc.append(1-m)
   if m>.35:
    dif.append([x0,y0,min(tw,x0+cell),min(h,y0+cell),round(m,3)])
    for y in range(y0,min(h,y0+cell)):
     for x in range(x0,min(tw,x0+cell)): out[y][x]=(220,40,40)
 score=round(100*(sum(score_acc)/len(score_acc) if score_acc else 1),1); rep={'mode':a.mode,'score':score,'regions':dif,'files':{'original':a.original,'clone':a.clone},'height_delta_pct':round((B[1]-A[1])*100/A[1],1) if A[1] else 0}
 if a.out: write_png(a.out,tw,h,out)
 print(json.dumps(rep,indent=2) if a.json else f"{a.mode} score: {score}/100; differing regions: {len(dif)}; height delta: {rep['height_delta_pct']}%")
 return 1 if a.fail_under is not None and score<a.fail_under else 0
if __name__=='__main__': sys.exit(main())
