<?php
function format_count($n) {
    if ($n < 1000) return (string)$n;
    $k = floor($n / 100) / 10;
    return str_replace('.0', '', (string)$k) . 'K';
}

function initials($name) {
    $words = explode(' ', trim($name));
    $init = '';
    foreach ($words as $w) {
        if (!empty($w)) {
            $init .= mb_strtoupper(mb_substr($w, 0, 1));
            if (mb_strlen($init) >= 2) break;
        }
    }
    return empty($init) ? 'U' : $init;
}

function get_site_stats($pdo) {
    $u = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND status = 'active'")->fetchColumn();
    $p = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $l = $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn();
    return ['users' => (int)$u, 'posts' => (int)$p, 'likes' => (int)$l];
}

function get_leaderboard($pdo, $limit = 10) {
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.avatar, u.department,
               (SELECT COALESCE(SUM(p.points), 0) FROM points p WHERE p.user_id = u.id) as total_points,
               (SELECT COUNT(*) FROM likes l JOIN posts po ON l.post_id = po.id WHERE po.user_id = u.id) as likes_received,
               (SELECT COUNT(*) FROM comments c JOIN posts po ON c.post_id = po.id WHERE po.user_id = u.id) as comments_received,
               (SELECT COUNT(*) FROM posts po WHERE po.user_id = u.id) as post_count,
               (SELECT po.type FROM posts po WHERE po.user_id = u.id ORDER BY po.created_at DESC LIMIT 1) as last_type
        FROM users u
        WHERE u.role = 'user' AND u.status = 'active'
        HAVING total_points > 0
        ORDER BY total_points DESC, u.name ASC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $rank = 1;
    $prev = null;
    $pos = 1;
    foreach ($results as &$row) {
        if ($prev !== null && $row['total_points'] < $prev) {
            $rank = $pos;
        }
        $row['rank'] = $rank;
        $prev = $row['total_points'];
        $pos++;
    }
    return $results;
}
