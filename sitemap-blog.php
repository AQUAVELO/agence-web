<?php
/**
 * Sitemap dynamique du blog (Blog.php + Post.php).
 * Soumettre dans Search Console : https://www.aquavelo.com/sitemap-blog.php
 */
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex', true);

require __DIR__ . '/Include/Database.php';

$base = 'https://www.aquavelo.com';
$today = date('Y-m-d');
$postsPerPage = 5;

function sitemap_escape(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function sitemap_url(string $loc, string $lastmod, string $changefreq, string $priority): string
{
    return "  <url>\n"
        . '    <loc>' . sitemap_escape($loc) . "</loc>\n"
        . '    <lastmod>' . sitemap_escape($lastmod) . "</lastmod>\n"
        . '    <changefreq>' . sitemap_escape($changefreq) . "</changefreq>\n"
        . '    <priority>' . sitemap_escape($priority) . "</priority>\n"
        . "  </url>\n";
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

echo sitemap_url($base . '/Blog.php', $today, 'weekly', '0.75');

if (!isset($con) || !$con) {
    echo '</urlset>';
    exit;
}

$posts = [];
$result = mysqli_query($con, 'SELECT post_id, post_date_time FROM aquavelo_post ORDER BY post_date_time DESC');
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
    mysqli_free_result($result);
}

$totalPosts = count($posts);
if ($totalPosts > $postsPerPage) {
    $totalPages = (int) ceil($totalPosts / $postsPerPage);
    for ($page = 2; $page <= $totalPages; $page++) {
        echo sitemap_url($base . '/Blog.php?page=' . $page, $today, 'weekly', '0.6');
    }
}

foreach ($posts as $post) {
    $postId = (int) ($post['post_id'] ?? 0);
    if ($postId <= 0) {
        continue;
    }
    $rawDate = $post['post_date_time'] ?? '';
    $lastmod = $today;
    if ($rawDate !== '') {
        $ts = strtotime($rawDate);
        if ($ts !== false) {
            $lastmod = date('Y-m-d', $ts);
        }
    }
    echo sitemap_url($base . '/Post.php?id=' . $postId, $lastmod, 'monthly', '0.65');
}

echo '</urlset>';
