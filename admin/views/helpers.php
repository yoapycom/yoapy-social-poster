<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// ===== Helpers & dados existentes =====================
if ( ! function_exists('ysp_net_svg') ) {
  function ysp_net_svg($net){
    $svg = array(
      'facebook'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7h-2.3V12h2.3V9.8c0-2.3 1.4-3.6 3.5-3.6 1 0 2 .2 2 .2v2.2h-1.1c-1.1 0-1.5.7-1.5 1.4V12h2.6l-.4 2.9h-2.2v7A10 10 0 0 0 22 12"/></svg>',
      'instagram'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.9a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2z"/></svg>',
      'youtube'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M23 7.5s-.2-1.6-.8-2.3c-.8-.8-1.7-.8-2.2-.9C16.9 4 12 4 12 4s-4.9 0-8 .3c-.5.1-1.4.1-2.2.9C1.2 5.9 1 7.5 1 7.5S.8 9.3.8 11.1v1.8c0 1.8.2 3.6.2 3.6s.2 1.6.8 2.3c.8.8 1.9.8 2.4.9C6.1 20 12 20 12 20s4.9 0 8-.3c.5-.1 1.4-.1 2.2-.9.6-.7.8-2.3.8-2.3s.2-1.8.2-3.6v-1.8c0-1.8-.2-3.6-.2-3.6zM9.8 15.3V8.7l6.1 3.3-6.1 3.3z"/></svg>',
      'tiktok'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M20 8.9a7.3 7.3 0 0 1-4.3-1.4v7.3a5.8 5.8 0 1 1-5.8-5.8c.5 0 .9 0 1.3.1v2.5a2.9 2.9 0 1 0 2.9 2.9V2h2.7a4.6 4.6 0 0 0 4.3 4.1v2.8z"/></svg>',
    );
    return $svg[$net] ?? '';
  }
}

if ( ! function_exists('ysp_net_chip') ) {
  function ysp_net_chip($net){
    $map = array(
      'facebook'  => 'bg-[#1877F2]/10 text-[#1877F2] border-[#1877F2]/20',
      'instagram' => 'bg-gradient-to-r from-[#f58529]/10 via-[#dd2a7b]/10 to-[#515bd4]/10 text-[#af2a7b] border-pink-300/30',
      'youtube'   => 'bg-[#FF0000]/10 text-[#FF0000] border-[#FF0000]/20',
      'tiktok'    => 'bg-gradient-to-r from-[#69C9D0]/10 via-black/10 to-[#EE1D52]/10 text-[#111] border-slate-300/40',
    );
    $label = ucfirst($net);
    $cls = isset($map[$net]) ? $map[$net] : 'bg-slate-100 text-slate-700 border-slate-200';
    return '<span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium '.$cls.'">'.ysp_net_svg($net).' <span>'.$label.'</span></span>';
  }
}

if ( ! function_exists('ysp_status_badge') ) {
  function ysp_status_badge($status){
    $s = strtolower((string)$status);
    $map = array(
      'complete'   => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
      'processing' => 'bg-amber-50 text-amber-700 ring-amber-200',
      'scheduled'  => 'bg-sky-50 text-sky-700 ring-sky-200',
      'error'      => 'bg-rose-50 text-rose-700 ring-rose-200',
      'pending'    => 'bg-slate-50 text-slate-700 ring-slate-200',
    );
    $cls = $map[$s] ?? $map['pending'];
    return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 '.$cls.'">'.esc_html($status ?: '—').'</span>';
  }
}