<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="css/microcms.css" rel="stylesheet" />
    <title>Bienvenue sur mon blog - Ma Jolie plume -</title>
</head>
<body>
    <header>
        <h1>Ma Jolie plume</h1>
    </header>
    <?php foreach ($posts as $post): ?>
    <article>
        <h2><?php echo $post->getSTitle() ?></h2>
        <p><?php echo $post->getSContent() ?></p>
    </article>
<?php endforeach ?>
<footer class="footer">
    <a href="https://github.com/belou971/writerBlog">lightCMS</a> is a light CMS built as minimalist writer blog for modern PHP development.
</footer>
</body>
</html>