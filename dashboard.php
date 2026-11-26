<?php require_once 'config/functions.php';
require_login();
$uid=user_id();
$today=date('Y-m-d');
$q=$pdo->prepare('SELECT COUNT(*) FROM journals WHERE user_id=?');
$q->execute([$uid]);$entries=$q->fetchColumn();
$q=$pdo->prepare('SELECT mood FROM moods WHERE user_id=? AND mood_date=?');
$q->execute([$uid,$today]);$mood=$q->fetchColumn();
$q=$pdo->prepare('SELECT h.id,h.name,IFNULL(l.completed,0) completed FROM habits h LEFT JOIN habit_logs l ON l.habit_id=h.id AND l.log_date=? WHERE h.user_id=?');
$q->execute([$today,$uid]);$habits=$q->fetchAll();
$done=count(array_filter($habits,fn($h)=>$h['completed']));
$q=$pdo->prepare('SELECT * FROM journals WHERE user_id=? ORDER BY entry_date DESC,id DESC LIMIT 3');
$q->execute([$uid]);$recent=$q->fetchAll();
page_header('Dashboard');
?><div class="hero"><span class="tag">Today · <?=date('D, d M Y')?></span><h2>Hello, <?=h($_SESSION['name'])?> ✨</h2>
  <p>Take a breath. Your thoughts, goals, and little moments belong here.</p>
  <a class="btn" href="journal.php?action=new">＋ Write today's journal</a></div><div class="grid grid-4"><div class="card"><div class="stat">✎
  <?= $entries?></div><div class="stat-label">Journal entries</div></div><div class="card"><div class="stat">☻ 
<?=h($mood?:'—')?></div><div class="stat-label">Today's mood</div><a class="small" href="mood.php">Record mood →</a></div>
    <div class="card"><div class="stat">✓ <?=$done?>/<?=count($habits)?></div><div class="stat-label">Habits completed today</div>
    </div><div class="card"><div class="stat">☆ <?=date('d')?></div><div class="stat-label">A fresh day to grow</div></div>
  </div><div class="grid grid-2" style="margin-top:18px"><div class="card"><h3>Today's habits</h3>
<?php if(!$habits):?><div class="empty">No habits yet. <a href="habits.php">Add your first habit</a>.</div><?php else:foreach($habits as $hbt):?><form method="post" action="habits.php" class="row" style="justify-content:space-between;padding:10px 0">
  <input type="hidden" name="csrf" value="<?=h(csrf())?>"><input type="hidden" name="habit_id" value="<?=$hbt['id']?>"><input type="hidden" name="log_date" value="<?=$today?>"><label><input type="checkbox" name="completed" value="1" <?=$hbt['completed']?'checked':''?> onchange="this.form.submit()"> <?=h($hbt['name'])?></label><span class="tag"><?=$hbt['completed']?'Done':'Pending'?></span></form><?php endforeach;endif;?></div><div class="card"><h3>Recent journal entries</h3><?php if(!$recent):?><div class="empty">Your first memory is waiting. <a href="journal.php?action=new">Write one</a>.</div><?php else:foreach($recent as $r):?><div class="entry"><h3><a href="journal.php?action=edit&id=<?=$r['id']?>"><?=h($r['title'])?></a></h3><div class="small"><?=h($r['entry_date'])?> · <?=h($r['mood']??'')?></div><p><?=h(mb_strimwidth($r['content'],0,140,'…'))?></p></div><?php endforeach;endif;?></div></div><?php page_footer();
