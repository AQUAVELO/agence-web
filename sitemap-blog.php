<?php
/**
 * Sitemap contenu éditorial (conseilminceur + blog legacy).
 * URL publique : https://www.aquavelo.com/sitemap-blog.xml
 */
declare(strict_types=1);

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=3600');
header('X-Robots-Tag: noindex', true);

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

function sitemap_blog_connect()
{
    $host = getenv('BLOG_MYSQL_HOST') ?: 'localhost';
    $user = getenv('BLOG_MYSQL_USER') ?: 'aquaveloblog';
    $pass = getenv('BLOG_MYSQL_PASSWORD') ?: 'e017D&xg';
    $db = getenv('BLOG_MYSQL_DB') ?: 'aquaveloblog';
    $port = (int) (getenv('BLOG_MYSQL_PORT') ?: 3306);

    mysqli_report(MYSQLI_REPORT_OFF);
    $con = @mysqli_connect($host, $user, $pass, $db, $port);

    return $con ?: null;
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Contenu éditorial principal (menus minceur + articles intégrés)
$xml .= sitemap_url($base . '/conseilminceur', $today, 'daily', '0.85');

$con = sitemap_blog_connect();
if ($con) {
    $xml .= sitemap_url($base . '/Blog.php', $today, 'weekly', '0.75');

    $posts = [];
    $result = mysqli_query($con, 'SELECT post_id, post_date_time FROM aquavelo_post ORDER BY post_date_time DESC');
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = $row;
        }
        mysqli_free_result($result);
    }
    mysqli_close($con);

    $totalPosts = count($posts);
    if ($totalPosts > $postsPerPage) {
        $totalPages = (int) ceil($totalPosts / $postsPerPage);
        for ($page = 2; $page <= $totalPages; $page++) {
            $xml .= sitemap_url($base . '/Blog.php?page=' . $page, $today, 'weekly', '0.6');
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
        $xml .= sitemap_url($base . '/Post.php?id=' . $postId, $lastmod, 'monthly', '0.65');
    }
}

$xml .= '</urlset>';
echo $xml;
