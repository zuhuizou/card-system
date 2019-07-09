<?php
namespace App\Http\Controllers\Admin; use App\Library\Helper; use Carbon\Carbon; use function foo\func; use Illuminate\Http\Request; use App\Http\Controllers\Controller; use App\Library\Response; class Pay extends Controller { function get(Request $spd5cc4d) { $spe440a8 = \App\Pay::orderBy('sort'); $spd508cb = $spd5cc4d->post('enabled'); if (strlen($spd508cb)) { $spe440a8->whereIn('enabled', explode(',', $spd508cb)); } $spc9965c = $spd5cc4d->post('search', false); $spee86b9 = $spd5cc4d->post('val', false); if ($spc9965c && $spee86b9) { if ($spc9965c == 'simple') { return Response::success($spe440a8->get(array('id', 'name'))); } elseif ($spc9965c == 'id') { $spe440a8->where('id', $spee86b9); } else { $spe440a8->where($spc9965c, 'like', '%' . $spee86b9 . '%'); } } $sp78c70b = $spe440a8->get(); return Response::success(array('list' => $sp78c70b, 'urls' => array('url' => config('app.url'), 'url_api' => config('app.url_api')))); } function stat(Request $spd5cc4d) { $this->validate($spd5cc4d, array('day' => 'required|integer|between:1,30')); $sp135b94 = (int) $spd5cc4d->input('day'); if ($sp135b94 === 30) { $sp584b3e = Carbon::now()->addMonths(-1); } else { $sp584b3e = Carbon::now()->addDays(-$sp135b94); } $sp78c70b = $this->authQuery($spd5cc4d, \App\Order::class)->where(function ($spe440a8) { $spe440a8->where('status', \App\Order::STATUS_PAID)->orWhere('status', \App\Order::STATUS_SUCCESS); })->where('paid_at', '>=', $sp584b3e)->with(array('pay' => function ($spe440a8) { $spe440a8->select(array('id', 'name')); }))->groupBy('pay_id')->selectRaw('`pay_id`,COUNT(*) as "count",SUM(`paid`) as "sum"')->get()->toArray(); $sp29a775 = array(); foreach ($sp78c70b as $sp338f71) { if (isset($sp338f71['pay']) && isset($sp338f71['pay']['name'])) { $spc23fd1 = $sp338f71['pay']['name']; } else { $spc23fd1 = '未知方式#' . $sp338f71['pay_id']; } $sp29a775[$spc23fd1] = array((int) $sp338f71['count'], (int) $sp338f71['sum']); } return Response::success($sp29a775); } function edit(Request $spd5cc4d) { $this->validate($spd5cc4d, array('id' => 'required|integer', 'name' => 'required|string', 'img' => 'required|string', 'driver' => 'required|string', 'way' => 'required|string', 'config' => 'required|string')); $spe00284 = (int) $spd5cc4d->post('id'); $spcc609a = $spd5cc4d->post('name'); $sp3f6d9e = $spd5cc4d->post('img'); $sp0f54c4 = $spd5cc4d->post('comment'); $sp5df6ee = $spd5cc4d->post('driver'); $sp8df86b = $spd5cc4d->post('way'); $spc27de0 = $spd5cc4d->post('config'); $spd508cb = (int) $spd5cc4d->post('enabled'); $sp1840d6 = \App\Pay::find($spe00284); if (!$sp1840d6) { $sp1840d6 = new \App\Pay(); } $sp1840d6->name = $spcc609a; $sp1840d6->img = $sp3f6d9e; $sp1840d6->comment = $sp0f54c4; $sp1840d6->driver = $sp5df6ee; $sp1840d6->way = $sp8df86b; $sp1840d6->config = $spc27de0; $sp1840d6->enabled = $spd508cb; $sp1840d6->fee_system = $spd5cc4d->post('fee_system'); $sp1840d6->saveOrFail(); return Response::success(); } function comment(Request $spd5cc4d) { $this->validate($spd5cc4d, array('id' => 'required|integer')); $spe00284 = (int) $spd5cc4d->post('id'); $sp1840d6 = \App\Pay::findOrFail($spe00284); $sp1840d6->comment = $spd5cc4d->post('comment'); $sp1840d6->save(); return Response::success(); } function sort(Request $spd5cc4d) { $this->validate($spd5cc4d, array('id' => 'required|integer')); $spe00284 = (int) $spd5cc4d->post('id'); $sp1840d6 = \App\Pay::findOrFail($spe00284); $sp1840d6->sort = (int) $spd5cc4d->post('sort', 1000); $sp1840d6->save(); return Response::success(); } function fee_system(Request $spd5cc4d) { $this->validate($spd5cc4d, array('id' => 'required|integer')); $spe00284 = (int) $spd5cc4d->post('id'); $sp1840d6 = \App\Pay::findOrFail($spe00284); $sp1840d6->fee_system = $spd5cc4d->post('fee_system'); $sp1840d6->saveOrFail(); return Response::success(); } function enable(Request $spd5cc4d) { $this->validate($spd5cc4d, array('ids' => 'required|string', 'enabled' => 'required|integer|between:0,3')); $spf46353 = $spd5cc4d->post('ids'); $spd508cb = (int) $spd5cc4d->post('enabled'); \App\Pay::whereIn('id', explode(',', $spf46353))->update(array('enabled' => $spd508cb)); return Response::success(); } function delete(Request $spd5cc4d) { $this->validate($spd5cc4d, array('id' => 'required|integer')); $spe00284 = (int) $spd5cc4d->post('id'); \App\Pay::whereId($spe00284)->delete(); return Response::success(); } }