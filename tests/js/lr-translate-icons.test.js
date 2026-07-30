const fs=require('fs'), path=require('path'), {JSDOM}=require('jsdom');
const ENGINE=path.join('/Volumes/CrucialMacExt/MacOffload/MAMP_htdocs/lion_roaring','public/frontend_assets/js/lr-translate.js');
const HTML=`<!doctype html><html><head><title>T</title></head><body>
  <div class="icon-heading" id="h1">Mail us</div>
  <div class="icon-heading" id="h2">Our Address</div>
  <div class="icon-heading" id="h3">Call us</div>
  <input class="form-control has-icon" id="inp" placeholder="Enter your billing address">
  <div class="upload-icon-wrap" id="wrap"><span id="wraptext">Drop files here to upload</span></div>
  <span class="material-icons" id="lig">cloud_upload</span>
  <span class="icon-bell" id="lig2">notifications</span>
  <i class="fas fa-user" id="fa">person</i>
</body></html>`;
const dom=new JSDOM(HTML,{url:'https://s.test/',pretendToBeVisual:true,runScripts:'dangerously'});
const w=dom.window; const sent=[];
w.fetch=(u,o)=>{const b=JSON.parse(o.body);b.items.forEach(t=>sent.push(t));
  return Promise.resolve({ok:true,json:()=>Promise.resolve({ok:true,target:b.target,translations:b.items.map(t=>'«'+t+'»')})});};
w.LR_TRANSLATE_CONFIG={endpoint:'/t',csrf:'x',cacheVersion:'t'};
w.localStorage.setItem('lr_content_lang','__original__');
const s=w.document.createElement('script'); s.textContent=fs.readFileSync(ENGINE,'utf8'); w.document.body.appendChild(s);
w.LrTranslate.setLanguage('es');
setTimeout(()=>{
  const $=id=>w.document.getElementById(id); const r=[];
  const ck=(l,p,d)=>r.push({l,p,d:d||''});
  ck('icon-heading "Mail us" translated', $('h1').textContent==='«Mail us»', $('h1').textContent);
  ck('icon-heading "Our Address" translated', $('h2').textContent==='«Our Address»', $('h2').textContent);
  ck('has-icon placeholder translated', $('inp').getAttribute('placeholder')==='«Enter your billing address»', $('inp').getAttribute('placeholder'));
  ck('text inside -icon-wrap translated', $('wraptext').textContent==='«Drop files here to upload»', $('wraptext').textContent);
  ck('material-icons ligature untouched', $('lig').textContent==='cloud_upload', $('lig').textContent);
  ck('loose icon ligature untouched', $('lig2').textContent==='notifications', $('lig2').textContent);
  ck('font-awesome <i> untouched', $('fa').textContent==='person', $('fa').textContent);
  ck('no ligature transmitted', !sent.includes('cloud_upload')&&!sent.includes('notifications')&&!sent.includes('person'));
  const f=r.filter(x=>!x.p);
  r.forEach(x=>console.log((x.p?'PASS  ':'FAIL  ')+x.l+(x.p?'':'  → '+x.d)));
  console.log('\n'+(r.length-f.length)+'/'+r.length+' passed');
  process.exit(f.length?1:0);
},600);
