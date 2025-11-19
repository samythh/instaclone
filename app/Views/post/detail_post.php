<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Detail Post | Instaclone</title>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <style>
            .image-detail .column { float: left; width: 45%; padding: 10px; height: 550px; background-color:#ffffff; }
            .image-detail .row:after { content: ""; display: table; clear: both; }
            .image-detail table { margin: 0 auto; width: 90%; }
            .image-detail .photo__header { border-bottom: 1px solid rgba(0,0,0,.0975); }
            .image-detail .photo__info { padding: 0; }
            .image-detail .photo__comments { height: 380px; overflow-y: auto; padding: 15px; }
            .image-detail .photo__comment { margin-bottom: 8px; }
            .image-detail .photo__add-comment-container { padding: 10px 0; border-top: 1px solid rgba(0,0,0,.0975); }
            .image-detail .photo__add-comment { width: 90%; border: none; }
            .image-detail .w3-circle { float: right; margin-top: -10px; }
        </style>
    </head>
    <body>
        <nav class="navigation">
            <a href="<?= site_url('feed/' . $currentUsername) ?>">
                <img src="<?= base_url('images/navLogo.png') ?>" alt="logo" title="logo" class="navigation__logo"/>
            </a>
            <div class="navigation__icons">
                <a href="<?= site_url('explore') ?>" class="navigation__link">
                    <i class="fa fa-compass"></i>
                </a>
                <a href="<?= site_url('post/create') ?>" class="navigation__link">
                    <i class="fa fa-plus-square-o"></i>
                </a>
                <a href="<?= site_url('profile/' . $currentUsername) ?>" class="navigation__link">
                    <i class="fa fa-user-o"></i>
                </a>
            </div>
        </nav>
        
        <main class="image-detail" style="margin-top: 100px;">
            <table>
            <tr>
                <td style="width: 60%;">
                    <div class="column" style="width: 100%;">
                        <img 
                            src="<?= base_url($post['photo']) ?>"
                            style="width:100%; height: auto;"
                            alt="Post Image"
                        />
                    </div>
                </td>
                
                <td style="width: 40%;">
                    <div class="column" style="width: 100%; height: auto;">
                        <div class="photo__header">
                            <img 
                                src="<?= base_url(empty($poster['profile_picture']) ? 'images/avatar.svg' : $poster['profile_picture']) ?>"
                                style="width:50px; height:50px"
                                class="photo__avatar"
                            />
                            <a href="<?= site_url('profile/' . $currentUsername . '/' . $poster['username']) ?>"><?= esc($poster['username']) ?></a>
                        </div>
                        
                        <div class="photo__info">
                            <div class="photo__comment" style="padding: 15px; border-bottom: 1px solid rgba(0,0,0,.0975);">
                                <span class="photo__comment-author"><?= esc($post['username']) ?></span> <?= esc($post['description']) ?>
                            </div>

                            <ul class="photo__comments" id="commentlist">
                            <?php foreach($comments as $comment): ?>
                                <li class="photo__comment">
                                    <span class="photo__comment-author"><?= esc($comment['commentername']) ?></span> <?= esc($comment['comment_text']) ?>
                                </li> 
                            <?php endforeach; ?>  
                            </ul>
                        </div>
                        
                        <div style="padding: 10px 15px; border-top: 1px solid rgba(0,0,0,.0975);">
                            <div class="photo__icons">
                                <span class="photo__icon">
                                    <a href="<?= site_url('post/like/' . $post['post_id']) ?>">
                                        <i class="fa fa-heart fa-lg <?= $isLiked ? 'heart-red' : 'fa-heart-o' ?>"></i>
                                    </a>
                                </span>
                            </div>
                            <span class="photo__likes"><?= esc($post['likes']) ?> likes</span>
                            <span class="photo__time-ago"><?= esc($timeAgo) ?></span>
                        </div>
                        
                        <div class="photo__add-comment-container">
                            <form action="<?= site_url('post/comment/' . $post['post_id']) ?>" method="post" id="myForm">
                                <textarea type="text" id="comment" name="comment" placeholder="Add a comment..." class="photo__add-comment"></textarea>
                                <input type="hidden" name="return_to" value="detail"> 
                                <button type="submit" class="w3-circle w3-blue" id="sub" style="width:50px; height:50px;">></button>
                            </form> 
                        </div>
                    </div>
                </td>
            </tr>
            </table>
        </main>
        
        <?= $this->include('partials/_footer') ?>
    </body>
</html>