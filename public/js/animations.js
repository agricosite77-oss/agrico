/* ==========================================================================
   AgriCo Sequential UI Animation System  —  animations.js  v2
   Core principle: ONE BY ONE. UPWARD. SMOOTH. SEQUENTIAL.
   Default entrance: opacity 0->1, translateY(24px)->0, blur(4px)->0.
   Easing: cubic-bezier(0.22,1,0.36,1)  Duration:450ms  Stagger:110ms
   Exposes window.AgriMotion plus helpers as globals.
   ========================================================================== */
(function (G) {
'use strict';
const EASE='cubic-bezier(0.22,1,0.36,1)',EASE_SOFT='cubic-bezier(0.16,1,0.3,1)',EASE_EXIT='cubic-bezier(0.4,0,1,1)';
const DUR_MICRO=180,DUR_FAST=280,DUR_ENTER=450,DUR_CARD=480,DUR_SLOW=600,STAGGER=110;
const RQ=G.matchMedia?G.matchMedia('(prefers-reduced-motion:reduce)'):{matches:false};
const reduced=()=>RQ.matches;

/* Core runner */
function run(el,frames,o={}){
  if(!el)return Promise.resolve();
  if(!el.animate){
    el.style.opacity='1';el.style.transform='none';el.style.filter='none';
    return Promise.resolve();
  }
  let{duration=300,delay=0,easing=EASE,fill='backwards'}=o;
  if(reduced()){frames=frames.filter(f=>'opacity'in f).map(f=>({opacity:f.opacity}));if(frames.length<2)return Promise.resolve();duration=Math.min(duration,100);delay=0;easing='ease-out';}
  try {
    const anim=el.animate(frames,{duration,delay,easing,fill});
    (el.__anims=el.__anims||new Set()).add(anim);
    const done=()=>el.__anims&&el.__anims.delete(anim);
    return anim.finished.then(done,done);
  } catch(e) {
    el.style.opacity='1';el.style.transform='none';el.style.filter='none';
    return Promise.resolve();
  }
}
function cancelAnimations(el){if(el&&el.__anims){el.__anims.forEach(a=>a.cancel());el.__anims.clear();}}

/* Frame builders */
const fIn=({y=24,blur=4,scale=0.98,x=0}={})=>[
  {opacity:0,transform:`translate(${x}px,${y}px) scale(${scale})`,filter:`blur(${blur}px)`},
  {opacity:1,transform:'translate(0,0) scale(1)',filter:'blur(0px)'}];
const fOut=({y=-8,blur=3,scale=0.99,x=0}={})=>[
  {opacity:1,transform:'translate(0,0) scale(1)',filter:'blur(0px)'},
  {opacity:0,transform:`translate(${x}px,${y}px) scale(${scale})`,filter:`blur(${blur}px)`}];

/* animateIn / animateOut */
function animateIn(el,o={}){
  if(typeof el==='string')el=document.querySelector(el);
  if(!el)return Promise.resolve();
  el.classList.add('is-revealed');el.hidden=false;
  // .ui-reveal is already animated by pure CSS (animations.css); replaying it via JS would run the entrance twice
  if(el.classList.contains('ui-reveal'))return Promise.resolve();
  return run(el,fIn({y:o.y??24,blur:o.blur??4,scale:o.scale??0.98,x:o.x??0}),{duration:o.duration??DUR_ENTER,delay:o.delay??0,easing:o.easing??EASE});
}
function animateOut(el,o={}){
  if(typeof el==='string')el=document.querySelector(el);
  if(!el)return Promise.resolve();
  return run(el,fOut({y:o.y??-8,blur:o.blur??3,scale:o.scale??0.99}),{duration:o.duration??200,delay:o.delay??0,easing:EASE_EXIT,fill:'forwards'}).then(()=>{cancelAnimations(el);if(o.remove)el.remove();else if(o.hide!==false)el.hidden=true;});
}
const animateCard=(el,o={})=>animateIn(el,Object.assign({duration:DUR_CARD,y:24,blur:3,scale:0.98},o));

/* animateSequence — THE heart: one by one, upward, staggered */
function animateSequence(container,o={}){
  if(typeof container==='string')container=document.querySelector(container);
  if(!container)return Promise.resolve();
  const rawStep=container.dataset.stagger!==undefined?+container.dataset.stagger:(o.step??STAGGER);
  const step=reduced()?0:Math.max(0,rawStep);
  const baseDelay=o.delay??0;
  const fn=o.fn||animateIn;
  const items=[...container.querySelectorAll(':scope > .ui-reveal')];
  if(o.force)items.forEach(el=>el.classList.remove('is-revealed'));
  const pending=items.filter(el=>!el.classList.contains('is-revealed'));
  return Promise.all(pending.map((el,i)=>{
    const delay=el.dataset.delay!==undefined?+el.dataset.delay:baseDelay+i*step;
    const dur=el.dataset.duration!==undefined?+el.dataset.duration:(o.duration??DUR_ENTER);
    return fn(el,{duration:dur,delay,y:o.y??24,blur:o.blur??4,scale:o.scale??0.98,...(o.options||{})});
  }));
}

function animateStagger(elements,o={}){
  const list=[...(typeof elements==='string'?document.querySelectorAll(elements):elements)];
  const step=reduced()?0:Math.max(0,o.step??STAGGER);
  const fn=o.fn||animateCard;
  return Promise.all(list.map((el,i)=>fn(el,Object.assign({},o.options,{delay:(o.delay??0)+i*step}))));
}

function revealSections(root=document,o={}){
  const step=reduced()?0:(o.step??STAGGER);
  const items=[...root.querySelectorAll('.ui-reveal')].filter(el=>!el.closest('[hidden]'));
  if(o.force)items.forEach(el=>el.classList.remove('is-revealed'));
  const pending=items.filter(el=>!el.classList.contains('is-revealed'));
  return Promise.all(pending.map((el,i)=>{
    const d=el.dataset.delay!==undefined?+el.dataset.delay:i*step;
    return animateIn(el,{delay:(o.delay??0)+Math.min(d,600)});
  }));
}

/* scrollReveal — Sequential entrance as elements scroll into viewport */
let _lastScrollRevealTime = 0;
function scrollReveal(root=document, selector='.ui-scroll-reveal, .ui-reveal:not(.is-revealed)'){
  if(!G.IntersectionObserver){
    (root.querySelectorAll ? root.querySelectorAll(selector) : []).forEach(el=>animateIn(el,{delay:0}));
    return null;
  }
  let batch=[], batchTimer=null;
  const flush=()=>{
    if(!batch.length) return;
    const current = batch.slice();
    batch = [];
    batchTimer = null;

    // Sort elements in visual reading order: top-to-bottom, then left-to-right
    current.sort((a,b)=>{
      const ra = a.getBoundingClientRect(), rb = b.getBoundingClientRect();
      if(Math.abs(ra.top - rb.top) > 24) return ra.top - rb.top;
      return ra.left - rb.left;
    });

    const now = performance.now();
    let baseDelay = Math.max(0, _lastScrollRevealTime - now);
    const step = reduced() ? 0 : STAGGER;

    current.forEach((el, i)=>{
      const seqParent = el.closest('.ui-sequence');
      const itemStep = seqParent && seqParent.dataset.stagger !== undefined ? +seqParent.dataset.stagger : step;
      const delay = baseDelay + (i * itemStep);
      animateIn(el, { delay });
    });

    _lastScrollRevealTime = now + baseDelay + (current.length * step);
  };

  const obs = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      if(!entry.isIntersecting || entry.target.classList.contains('is-revealed')) return;
      obs.unobserve(entry.target);
      batch.push(entry.target);
      clearTimeout(batchTimer);
      batchTimer = setTimeout(flush, 45);
    });
  }, {
    root: null,
    rootMargin: '0px 0px -40px 0px',
    threshold: [0, 0.08]
  });

  const observeElements = (container) => {
    if(!container || !container.querySelectorAll) return;
    container.querySelectorAll(selector).forEach(el => {
      if(!el.classList.contains('is-revealed')) {
        obs.observe(el);
      }
    });
  };

  observeElements(root);
  obs.observeElements = observeElements;
  return obs;
}

/* revealTable */
function revealTable(container,o={}){
  if(typeof container==='string')container=document.querySelector(container);
  if(!container)return Promise.resolve();
  const step=reduced()?0:(o.step??45);
  const rows=[...container.querySelectorAll('.ui-table-row')].filter(el=>!el.classList.contains('is-revealed'));
  return Promise.all(rows.map((el,i)=>animateIn(el,{duration:DUR_FAST,delay:i*step,y:12,blur:1,scale:1})));
}

/* animateDashboard */
function autoDetectDashboardSteps(c){
  return[
    {selector:'.page-header,.dash-header,[data-ui-step="header"]'},
    {selector:'.page-title,h1,[data-ui-step="title"]'},
    {selector:'.page-subtitle,[data-ui-step="subtitle"]'},
    {selector:'.stat-card,.kpi-card,.summary-card,[data-ui-step="stats"]',step:110},
    {selector:'.chart-wrapper,.main-chart,[data-ui-step="chart"]'},
    {selector:'.secondary-card,.info-card,[data-ui-step="secondary"]',step:110},
    {selector:'.recent-table,.activity-list,[data-ui-step="table"]'},
    {selector:'.map-container,[data-ui-step="map"]'},
  ].filter(p=>c.querySelector(p.selector));
}
function animateDashboard(config={}){
  const container=config.container?document.querySelector(config.container):(document.querySelector('#appMainContent')||document.querySelector('.ws-main'));
  if(!container)return revealSections();
  const steps=config.steps||autoDetectDashboardSteps(container);
  let cursor=0;
  const next=(delay=0)=>{
    if(cursor>=steps.length)return Promise.resolve();
    const s=steps[cursor++];
    const els=typeof s.selector==='string'?[...container.querySelectorAll(s.selector)]:(Array.isArray(s.selector)?s.selector:[]);
    if(!els.length)return Promise.resolve(next());
    if(els.length===1||s.stagger===false)return animateIn(els[0],{delay}).then(()=>next());
    const stepMs=reduced()?0:(s.step??STAGGER);
    const lastDelay=delay+(els.length-1)*stepMs;
    Promise.all(els.map((el,i)=>animateIn(el,{delay:delay+i*stepMs})));
    return new Promise(r=>setTimeout(r,lastDelay+DUR_ENTER*0.6)).then(()=>next());
  };
  return next();
}

/* animatePageTransition */
async function animatePageTransition(oldPage,newPage,o={}){
  if(oldPage===newPage)return;
  if(oldPage&&!oldPage.hidden){
    await run(oldPage,fOut({y:-5,blur:2,scale:1}),{duration:170,easing:EASE_EXIT,fill:'forwards'});
    cancelAnimations(oldPage);oldPage.hidden=true;
  }
  if(newPage){
    newPage.hidden=false;newPage.classList.add('is-revealed');
    run(newPage,fIn({y:15,blur:2,scale:1}),{duration:360});
    setTimeout(()=>revealSections(newPage,{force:true}),60);
  }
}

/* animateModal */
async function animateModal(modal,open){
  if(typeof modal==='string')modal=document.querySelector(modal);
  if(!modal)return;
  const backdrop=modal.querySelector('.ui-modal__backdrop'),panel=modal.querySelector('.ui-modal__panel');
  if(open){
    modal.__lastFocus=document.activeElement;modal.hidden=false;modal.setAttribute('aria-hidden','false');
    run(backdrop,[{opacity:0},{opacity:1}],{duration:250});
    await run(panel,[{opacity:0,transform:'translateY(18px) scale(0.96)',filter:'blur(4px)'},{opacity:1,transform:'translateY(0) scale(1)',filter:'blur(0px)'}],{duration:380,delay:40});
    panel.querySelectorAll('.ui-reveal').forEach((el,i)=>animateIn(el,{delay:80+i*60,y:12,blur:2}));
    const f=panel.querySelector('button,[href],input,[tabindex]');if(f)f.focus({preventScroll:true});
  }else{
    run(backdrop,[{opacity:1},{opacity:0}],{duration:200,easing:EASE_EXIT,fill:'forwards'});
    await run(panel,[{opacity:1,transform:'translateY(0) scale(1)'},{opacity:0,transform:'translateY(10px) scale(0.97)'}],{duration:200,easing:EASE_EXIT,fill:'forwards'});
    cancelAnimations(backdrop);cancelAnimations(panel);modal.hidden=true;modal.setAttribute('aria-hidden','true');
    if(modal.__lastFocus&&modal.__lastFocus.focus)modal.__lastFocus.focus({preventScroll:true});
  }
}

/* animateDropdown */
async function animateDropdown(menu,open){
  if(typeof menu==='string')menu=document.querySelector(menu);
  if(!menu)return;
  const items=[...menu.querySelectorAll('.ui-dropdown__item')];
  if(open){
    menu.hidden=false;
    await run(menu,[{opacity:0,transform:'translateY(-6px) scale(0.97)'},{opacity:1,transform:'translateY(0) scale(1)'}],{duration:200});
    const step=reduced()?0:35;
    items.forEach((it,i)=>run(it,fIn({y:8,blur:1,scale:1}),{duration:DUR_FAST,delay:i*step}));
  }else{
    await run(menu,[{opacity:1,transform:'translateY(0) scale(1)'},{opacity:0,transform:'translateY(-5px) scale(0.98)'}],{duration:140,easing:EASE_EXIT,fill:'forwards'});
    cancelAnimations(menu);menu.hidden=true;items.forEach(it=>{cancelAnimations(it);it.style.opacity='';});
  }
}

/* animateMobileMenu */
async function animateMobileMenu(menu,open){
  if(typeof menu==='string')menu=document.querySelector(menu);
  if(!menu)return;
  const panel=menu.querySelector('.ui-mobile-menu__panel'),items=[...menu.querySelectorAll('.ui-mobile-menu__item')];
  if(open){
    menu.hidden=false;
    run(menu,[{opacity:0},{opacity:1}],{duration:250});
    await run(panel,fIn({y:30,blur:4,scale:0.97}),{duration:350,delay:30});
    const step=reduced()?0:55;items.forEach((it,i)=>run(it,fIn({y:10,blur:1,scale:1}),{duration:DUR_FAST,delay:60+i*step}));
  }else{
    await run(panel,fOut({y:20,blur:3,scale:0.97}),{duration:220,easing:EASE_EXIT,fill:'forwards'});
    run(menu,[{opacity:1},{opacity:0}],{duration:200,easing:EASE_EXIT,fill:'forwards'});
    await new Promise(r=>setTimeout(r,200));
    cancelAnimations(menu);cancelAnimations(panel);menu.hidden=true;items.forEach(it=>cancelAnimations(it));
  }
}

/* showToast */
function showToast(message,o={}){
  let region=document.querySelector('.ui-toast-region');
  if(!region){region=document.createElement('div');region.className='ui-toast-region';region.setAttribute('role','status');region.setAttribute('aria-live','polite');document.body.appendChild(region);}
  const t=document.createElement('div');t.className='ui-toast'+(o.type==='warn'?' ui-toast--warn':'');t.textContent=message;region.appendChild(t);
  run(t,[{opacity:0,transform:'translateY(15px)',filter:'blur(2px)'},{opacity:1,transform:'translateY(0)',filter:'blur(0px)'}],{duration:300});
  setTimeout(async()=>{await run(t,[{opacity:1},{opacity:0}],{duration:200,easing:EASE_EXIT,fill:'forwards'});t.remove();},(o.duration??3000));
  return t;
}

/* ==========================================================================
   AgriCo Premium Skeleton Engine (Sections 41 - 77)
   - Minimum display time (180ms) to prevent flickering on fast connections
   - Smooth cross-fade from skeleton to real content
   - Automatic sequential rise animation on real content (110ms upward stagger)
   - Zero spinner reliance
   ========================================================================== */

async function skeletonReveal(wrap,o={}){
  if(typeof wrap==='string')wrap=document.querySelector(wrap);if(!wrap)return;
  const minShow=o.minShow??180,shown=wrap._skelShown||Date.now(),wait=Math.max(0,minShow-(Date.now()-shown));
  if(wait>0)await new Promise(r=>setTimeout(r,wait));
  const allSkels=wrap.querySelectorAll('.ui-skel, .skeleton, .skeleton-card, .skeleton-chart-box, .skeleton-table-box, .skeleton-map-box, .skeleton-soil-result');
  if(allSkels.length){
    await Promise.all([...allSkels].map(s=>run(s,[{opacity:1},{opacity:0}],{duration:180,easing:EASE_EXIT,fill:'forwards'})));
  }
  wrap.dataset.loaded='true';
  const real=wrap.querySelector('.ui-real, .real-content')||wrap;
  if(real){real.style.opacity='1';real.hidden=false;}
  const reals=[...wrap.querySelectorAll('.ui-real .ui-reveal, .ui-real.ui-reveal, .real-content .ui-reveal')];
  if(reals.length>0){
    const step=reduced()?0:(o.step??STAGGER);
    return Promise.all(reals.map((el,i)=>animateIn(el,{delay:i*step,y:20,blur:3})));
  }else if(real&&real!==wrap){
    return animateIn(real,{y:20,blur:3});
  }
}

async function transitionSkeleton(skeletonEl,contentEl,o={}){
  if(typeof skeletonEl==='string')skeletonEl=document.querySelector(skeletonEl);
  if(typeof contentEl==='string')contentEl=document.querySelector(contentEl);
  const minShow=o.minShow??180,startTime=o.startTime||Date.now(),elapsed=Date.now()-startTime,wait=Math.max(0,minShow-elapsed);
  if(wait>0)await new Promise(r=>setTimeout(r,wait));
  if(skeletonEl){
    await run(skeletonEl,[{opacity:1},{opacity:0}],{duration:180,easing:EASE_EXIT,fill:'forwards'});
    skeletonEl.style.display='none';cancelAnimations(skeletonEl);
  }
  if(contentEl){
    contentEl.style.display='';contentEl.style.opacity='1';contentEl.hidden=false;
    const items=[...contentEl.querySelectorAll('.ui-reveal')];
    if(items.length>0){
      const step=reduced()?0:(o.step??STAGGER);
      return Promise.all(items.map((el,i)=>animateIn(el,{delay:i*step,y:20,blur:3})));
    }else{
      return animateIn(contentEl,{y:20,blur:3});
    }
  }
}

/* Text */
const TV={heading:{duration:300,y:10,blur:4,scale:0.98},soft:{duration:260,y:6,blur:2,scale:1}};
function ensureText(el){
  if(el.__ut)return el.__ut;el.classList.add('animated-text');
  let cur=el.querySelector(':scope>.animated-text__current');
  if(!cur){cur=document.createElement('span');cur.className='animated-text__current';while(el.firstChild)cur.appendChild(el.firstChild);el.appendChild(cur);}
  const stale=el.querySelector(':scope>.animated-text__next');if(stale)stale.remove();
  cur.dataset.text=cur.textContent.trim();return(el.__ut={cur,old:null,token:0});
}
function settleText(el,st){cancelAnimations(st.cur);if(st.old){cancelAnimations(st.old);st.old.remove();st.old=null;}el.classList.remove('is-animating');}
function animateText(el,newText,opts={}){
  if(typeof el==='string')el=document.querySelector(el);if(!el)return Promise.resolve();
  newText=String(newText);const st=ensureText(el);if(st.old)settleText(el,st);
  if(st.cur.dataset.text===newText)return Promise.resolve();
  const vk=opts.variant||(el.matches('p,small,.ui-text-soft')?'soft':'heading');
  const v=Object.assign({},TV[vk]||TV.heading,opts);const token=++st.token;
  const old=st.cur,next=document.createElement('span');
  next.className='animated-text__next';next.dataset.text=newText;next.textContent=newText;
  old.setAttribute('aria-hidden','true');el.classList.add('is-animating');el.appendChild(next);
  const jobs=[
    run(old,fOut({y:-v.y,blur:v.blur,scale:v.scale}),{duration:v.duration*0.85,easing:EASE_EXIT,fill:'forwards'}),
    run(next,fIn({y:v.y,blur:v.blur,scale:v.scale}),{duration:v.duration,delay:60})];
  st.old=old;st.cur=next;
  return Promise.all(jobs).then(()=>{if(st.token!==token)return;next.classList.replace('animated-text__next','animated-text__current');old.remove();st.old=null;el.classList.remove('is-animating');});
}
const animateValue=(el,value,opts)=>animateText(el,value,Object.assign({variant:'soft'},opts));

/* animateNumber */
function animateNumber(el,value,o={}){
  if(typeof el==='string')el=document.querySelector(el);
  const decimals=o.decimals??((String(value).split('.')[1]||'').length),from=parseFloat(el.textContent)||0,fmt=n=>n.toFixed(decimals);
  if(reduced()||from===value){el.textContent=fmt(+value);return Promise.resolve();}
  const dur=o.duration??600,t0=performance.now();
  return new Promise(resolve=>{(function tick(now){const p=Math.min((now-t0)/dur,1),e=1-Math.pow(1-p,4);el.textContent=fmt(from+(value-from)*e);if(p<1)requestAnimationFrame(tick);else resolve();})(t0);});
}

/* animateCount — Staggered counter for whole numbers only with delay, suffix, and value change tracking */
function animateCount(el, target, options = {}) {
  if (typeof el === 'string') el = document.querySelector(el);
  if (!el) return;
  const num = Math.round(Number(target));
  if (isNaN(num)) return;

  const suffix = options.suffix || '';
  const prefix = options.prefix || '';
  const duration = options.duration ?? 850;
  const delay = options.delay ?? 0;

  // If the element already displays this target, don't replay (prevents blinking on polling)
  if (el.__animatedValue === num) return;
  // After the entrance has finished, freshly inserted elements (Livewire morphs) show their value instantly
  if (document.documentElement.classList.contains('ui-settled') && el.__animatedValue === undefined) {
    el.__animatedValue = num;
    el.textContent = prefix + Math.round(num).toLocaleString() + suffix;
    return;
  }
  const from = (el.__animatedValue !== undefined) ? el.__animatedValue : 0;
  el.__animatedValue = num;

  const format = n => Math.round(n).toLocaleString();

  if (reduced()) {
    el.textContent = prefix + format(num) + suffix;
    return;
  }

  if (el.__countTimer) clearTimeout(el.__countTimer);
  if (el.__countRaf) cancelAnimationFrame(el.__countRaf);

  if (from === 0 && delay > 0) {
    el.textContent = prefix + format(0) + suffix;
  }

  el.__countTimer = setTimeout(() => {
    const startTime = performance.now();
    function tick(now) {
      const elapsed = Math.min(now - startTime, duration);
      const progress = duration > 0 ? elapsed / duration : 1;
      const ease = 1 - Math.pow(1 - progress, 3);
      const current = Math.round(from + (num - from) * ease);
      el.textContent = prefix + format(current) + suffix;

      if (elapsed < duration) {
        el.__countRaf = requestAnimationFrame(tick);
      } else {
        el.textContent = prefix + format(num) + suffix;
        el.__countRaf = null;
      }
    }
    el.__countRaf = requestAnimationFrame(tick);
  }, delay);
}

/* animateImage */
const animateImage=(el,o={})=>{
  if(typeof el==='string')el=document.querySelector(el);if(!el)return Promise.resolve();
  el.classList.add('is-revealed');
  return run(el,[{opacity:0,transform:'scale(1.02) translateY(15px)',filter:'blur(3px)'},{opacity:1,transform:'scale(1) translateY(0)',filter:'blur(0px)'}],{duration:o.duration??DUR_SLOW,delay:o.delay??0,easing:EASE_SOFT});
};

/* animateMarker(s) */
const animateMarker=(el,o={})=>run(el,[{opacity:0,transform:'translateY(15px) scale(0.8)'},{opacity:1,transform:'translateY(0) scale(1)'}],{duration:350,delay:o.delay??0});
function animateMarkers(container,o={}){
  const markers=[...(typeof container==='string'?document.querySelectorAll(container):container.querySelectorAll('.ui-marker'))];
  const step=reduced()?0:(o.step??100);
  return Promise.all(markers.map((el,i)=>animateMarker(el,{delay:i*step})));
}

/* animateChart */
function animateChart(root){
  if(typeof root==='string')root=document.querySelector(root);if(!root)return Promise.resolve();
  const jobs=[];
  root.querySelectorAll('.ui-chart__line').forEach(p=>{const len=p.getTotalLength?p.getTotalLength():300;p.style.strokeDasharray=len;jobs.push(run(p,[{strokeDashoffset:len},{strokeDashoffset:0}],{duration:700,delay:150,easing:EASE_SOFT,fill:'backwards'}));});
  root.querySelectorAll('.ui-chart__bar').forEach((b,i)=>jobs.push(run(b,[{opacity:0,transform:'scaleY(0)'},{opacity:0.85,transform:'scaleY(1)'}],{duration:450,delay:150+(reduced()?0:i*40)})));
  root.querySelectorAll('.ui-chart__label').forEach((l,i)=>jobs.push(run(l,[{opacity:0},{opacity:1}],{duration:300,delay:100+(reduced()?0:i*25)})));
  root.querySelectorAll('.ui-chart__value').forEach((v,i)=>jobs.push(run(v,[{opacity:0,transform:'translateY(4px)'},{opacity:1,transform:'translateY(0)'}],{duration:300,delay:200+(reduced()?0:i*30)})));
  return Promise.all(jobs);
}

/* animateSelect */
function animateSelect(el){
  const g=el.parentElement;if(g)g.querySelectorAll('.is-selected').forEach(s=>{s.classList.remove('is-selected');s.removeAttribute('aria-pressed');});
  el.classList.add('is-selected');el.setAttribute('aria-pressed','true');
  return run(el,[{opacity:0.75,transform:'scale(0.96)'},{opacity:1,transform:'scale(1)'}],{duration:220});
}

/* animateNavIndicator */
function animateNavIndicator(item,o={}){
  const nav=item.closest('.ui-nav');if(!nav)return;
  const ind=nav.querySelector('.ui-nav-indicator');
  nav.querySelectorAll('.ui-nav-item').forEach(n=>{const on=n===item;n.classList.toggle('is-active',on);if(on)n.setAttribute('aria-current','page');else n.removeAttribute('aria-current');});
  if(ind){
    if(o.instant||reduced())ind.classList.add('is-instant');
    ind.style.width=`${item.offsetWidth}px`;ind.style.height=`${item.offsetHeight}px`;
    ind.style.transform=`translate(${item.offsetLeft}px,${item.offsetTop}px)`;
    if(o.instant||reduced())requestAnimationFrame(()=>requestAnimationFrame(()=>ind.classList.remove('is-instant')));
  }
  if(o.instant)return;
  const icon=item.querySelector('.ui-nav-item__icon'),label=item.querySelector('.ui-nav-item__label');
  if(icon)run(icon,[{opacity:0.7,transform:'scale(0.92)'},{opacity:1,transform:'scale(1)'}],{duration:260,delay:40});
  if(label)run(label,[{opacity:0.6,transform:'translateX(-3px)'},{opacity:1,transform:'translateX(0)'}],{duration:240,delay:60});
}

/* animateContent */
async function animateContent(el,update){
  await run(el,fOut({y:-6,blur:3,scale:1}),{duration:140,easing:EASE_EXIT,fill:'forwards'});
  cancelAnimations(el);await update(el);
  return run(el,fIn({y:6,blur:3,scale:1}),{duration:300});
}

/* ---- Site-wide auto layer ---- */
const MAIN_SEL='#appMainContent, .ws-main';
function usableChildren(parent){return[...parent.children].filter(el=>{if(/^(SCRIPT|STYLE|TEMPLATE|LINK)$/.test(el.tagName))return false;const cs=getComputedStyle(el);return cs.display!=='none'&&cs.position!=='fixed';});}

function autoReveal(root=document){
  try {
    const mains = root.querySelectorAll ? root.querySelectorAll(MAIN_SEL) : [];
    const vh = window.innerHeight || document.documentElement.clientHeight || 800;

    mains.forEach(main=>{
      // Priority 1: if explicit .ui-reveal elements exist inside main, CSS handles the sequence
      const allReveals = [...main.querySelectorAll('.ui-reveal')];
      if(allReveals.length > 0){
        if(_scrollObs){
          allReveals.forEach(el => {
            const r = el.getBoundingClientRect();
            if(r.top >= vh + 100) _scrollObs.observe(el);
          });
        }
        return;
      }

      // Priority 2: if .ui-sequence containers exist without .ui-reveal, chain them one after another
      const seqs = [...main.querySelectorAll('.ui-sequence')].filter(s => !s.dataset.uiSeqDone);
      if(seqs.length > 0){
        let cumulativeDelay = 0;
        seqs.forEach(seq=>{
          seq.dataset.uiSeqDone = '1';
          const rawStep = seq.dataset.stagger !== undefined ? +seq.dataset.stagger : STAGGER;
          const items = [...seq.querySelectorAll(':scope > .ui-reveal, :scope > *')].filter(k => !k.classList.contains('is-revealed'));
          items.forEach((item, idx)=>{
            const r = item.getBoundingClientRect();
            if(r.top < vh + 50){
              animateIn(item, { delay: cumulativeDelay + idx * rawStep });
            } else if(_scrollObs){
              _scrollObs.observe(item);
            }
          });
          cumulativeDelay += items.length * rawStep;
        });
        return;
      }

      // Fallback: search for standard cards/sections if not already performed on this container
      if(main.dataset.uiAutoDone === '1') return;
      main.dataset.uiAutoDone = '1';

      let targets = [...main.querySelectorAll('.auth-card, .login-card, .ws-card, .profile-info-card, form > .input-group')];
      if(targets.length === 0){
        let host = main, kids = usableChildren(host);
        for(let d = 0; d < 4 && kids.length === 1; d++){ host = kids[0]; kids = usableChildren(host); }
        targets = kids.slice(0, 12);
      }
      const step = reduced() ? 0 : STAGGER;
      targets.forEach((el, i)=>{
        const r = el.getBoundingClientRect();
        if(r.top < vh + 50){
          el.classList.add('is-revealed');
          run(el, fIn({ y: 24, blur: 4, scale: 0.98 }), { duration: DUR_ENTER, delay: i * step });
        } else if(_scrollObs){
          _scrollObs.observe(el);
        }
      });
    });
  } catch(err) {
    console.warn('[AgriMotion autoReveal error]', err);
  } finally {
    document.documentElement.classList.remove('ui-booting');
  }
}

function autoSequences(root=document){
  try {
    root.querySelectorAll('.ui-sequence').forEach(seq=>{
      if(seq.dataset.uiSeqDone === '1') return;
      seq.dataset.uiSeqDone = '1';
      animateSequence(seq);
    });
  } catch(err) {
    console.warn('[AgriMotion autoSequences error]', err);
  }
}

function enhanceSidebar(){
  try {
    const nav = document.querySelector('#appSidebar nav');
    if(!nav) return;
    const active = nav.querySelector('.sidebar-nav-link.is-active');
    if(!active) return;
    let ind = nav.querySelector('.ui-nav-indicator');
    if(!ind){
      nav.style.position = 'relative';
      nav.style.isolation = 'isolate';
      ind = document.createElement('div');
      ind.className = 'ui-nav-indicator is-instant';
      ind.setAttribute('aria-hidden', 'true');
      ind.style.zIndex = '-1';
      ind.style.margin = '0';
      nav.appendChild(ind);
      document.documentElement.classList.add('ui-nav-ind');
    }
    const place = el => {
      if(!el) return;
      ind.style.width = el.offsetWidth + 'px';
      ind.style.height = el.offsetHeight + 'px';
      ind.style.transform = `translate(${el.offsetLeft}px,${el.offsetTop}px)`;
    };
    const href = active.getAttribute('href');
    let prevHref = null;
    try {
      prevHref = sessionStorage.getItem('agrico.sidebar.prev');
      sessionStorage.setItem('agrico.sidebar.prev', href || '');
    } catch(e) {}
    const prev = prevHref && prevHref !== href ? [...nav.querySelectorAll('.sidebar-nav-link')].find(a => a.getAttribute('href') === prevHref) : null;
    place(prev || active);
    void ind.offsetWidth;
    requestAnimationFrame(() => {
      ind.classList.remove('is-instant');
      if(prev && !reduced()) place(active);
      else {
        ind.classList.add('is-instant');
        place(active);
        requestAnimationFrame(() => ind.classList.remove('is-instant'));
      }
    });
    if(G.ResizeObserver && !nav.__ro){
      nav.__ro = true;
      new ResizeObserver(() => {
        ind.classList.add('is-instant');
        place(nav.querySelector('.sidebar-nav-link.is-active') || active);
        requestAnimationFrame(() => requestAnimationFrame(() => ind.classList.remove('is-instant')));
      }).observe(nav);
    }
  } catch(err) {
    console.warn('[AgriMotion enhanceSidebar error]', err);
  }
}

let _scrollObs = null;
function init(){
  try {
    document.documentElement.classList.add('ui-motion');
    document.querySelectorAll('.ui-animated-text').forEach(ensureText);
    enhanceSidebar();

    // 1. Initialize scroll observer for scroll reveal elements only
    if(_scrollObs){ try { _scrollObs.disconnect(); } catch(e) {} }
    _scrollObs = scrollReveal(document, '.ui-scroll-reveal');

    // 2. Animate in-viewport elements on load
    autoReveal(document);
    autoSequences(document);
  } catch(err) {
    console.warn('[AgriMotion init error]', err);
  } finally {
    document.documentElement.classList.remove('ui-booting');
  }
}

/* ---- Livewire 3 Integration & Preservation ---- */
function setupLivewireHooks(){
  if(!window.Livewire || !window.Livewire.hook || window.__agriLwHooked) return;
  window.__agriLwHooked = true;

  // 1. Critical: preserve ui-finished class across Livewire DOM morphs (wire:poll, actions, inputs)
  window.Livewire.hook('morph.updating', ({ el, toEl }) => {
    try {
      if(el && el.classList && el.classList.contains('ui-finished')) {
        if(toEl && toEl.classList) {
          toEl.classList.add('ui-finished');
        }
      }
    } catch(e) {}
  });

  // 2. Newly added Livewire elements
  window.Livewire.hook('morph.added', ({ el }) => {
    try {
      if(!el || el.nodeType !== 1) return;
      if(el.classList && el.classList.contains('ui-scroll-reveal') && !el.classList.contains('is-revealed')) {
        animateIn(el, { delay: 40 });
      }
    } catch(e) {}
  });

  // 3. Post-commit sweep
  window.Livewire.hook('commit', ({ succeed }) => {
    succeed(() => {
      requestAnimationFrame(() => {
        try { autoReveal(document); } catch(e) {}
      });
    });
  });
}

if(window.Livewire) {
  setupLivewireHooks();
} else {
  document.addEventListener('livewire:init', setupLivewireHooks);
  document.addEventListener('livewire:initialized', setupLivewireHooks);
}

/* Listen to animationend: ONLY IF THAT ANIMATION FINISH, mark as ui-finished so it never redisplays */
document.addEventListener('animationend', (e) => {
  const el = e.target;
  if (!el || !el.classList) return;
  const name = e.animationName;
  if (name === 'ui-reveal' || name === 'ui-reveal-text' || name === 'ui-reveal-num' || name === 'ui-ring' || name === 'ui-growx') {
    el.classList.add('ui-finished');
  }
}, true);

/* ---- DOM MutationObserver (catches dynamic DOM inserts) ---- */
let _moDebounce = null;
if(typeof MutationObserver !== 'undefined'){
  const mo = new MutationObserver(mutations => {
    let hasNew = false;
    for(const m of mutations){
      if(m.addedNodes && m.addedNodes.length){
        for(const n of m.addedNodes){
          if(n.nodeType === 1){
            if(n.classList && n.classList.contains('ui-scroll-reveal') && !n.classList.contains('is-revealed')){
              hasNew = true; break;
            }
          }
        }
      }
      if(hasNew) break;
    }
    if(hasNew){
      clearTimeout(_moDebounce);
      _moDebounce = setTimeout(() => {
        try { autoReveal(document); } catch(e) {}
      }, 40);
    }
  });

  if(document.body){
    mo.observe(document.body, { childList: true, subtree: true });
  } else {
    document.addEventListener('DOMContentLoaded', () => {
      if(document.body) mo.observe(document.body, { childList: true, subtree: true });
    });
  }
}

/* ---- Universal Safety Watchdog ---- */
setTimeout(() => {
  try {
    document.documentElement.classList.remove('ui-booting');
    const vh = window.innerHeight || document.documentElement.clientHeight || 800;
    document.querySelectorAll('#appMainContent > *, .ws-main > *').forEach(c => {
      c.style.opacity = '';
      c.style.pointerEvents = '';
    });
  } catch(e) {}
}, 1200);

const api={animateIn,animateOut,animateCard,animateSequence,animateStagger,revealSections,animateDashboard,scrollReveal,revealTable,animatePageTransition,animateNavIndicator,animateModal,animateDropdown,animateMobileMenu,showToast,skeletonReveal,transitionSkeleton,animateText,animateValue,animateNumber,animateCount,animateImage,animateMarker,animateMarkers,animateChart,animateSelect,animateContent,isReducedMotion:reduced,init};
G.AgriMotion=api;
Object.assign(G,api);
G.animateCount = animateCount;
delete G.isReducedMotion;G.AgriMotion.isReducedMotion=reduced;

/* Settle watchdog: after entrance sequence finishes, mark any remaining reveal elements as finished */
let _settleTimer=null;
function scheduleSettle(){
  clearTimeout(_settleTimer);
  _settleTimer=setTimeout(()=>{
    document.querySelectorAll('.ui-reveal, .ui-reveal-text, .ui-reveal-num, .ui-ring, .ui-bar-x').forEach(el=>el.classList.add('ui-finished'));
  }, 2500);
}
document.addEventListener('livewire:navigating',()=>{
  clearTimeout(_settleTimer);
  document.querySelectorAll('.ui-finished').forEach(el=>el.classList.remove('ui-finished'));
});
scheduleSettle();

if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
document.addEventListener('livewire:navigated',()=>{
  document.querySelectorAll('.ui-finished').forEach(el=>el.classList.remove('ui-finished'));
  scheduleSettle();
  document.querySelectorAll('[data-ui-auto],[data-ui-auto-done],[data-ui-seq-done]').forEach(el=>{
    delete el.dataset.uiAuto;
    delete el.dataset.uiAutoDone;
    delete el.dataset.uiSeqDone;
  });
  init();
});
})(window);
