INSERT INTO t_admin (adm_login, adm_email, adm_pwd, adm_web_name) VALUES ('adminproj3', 'belou.atlantis@gmail.com', 'test_proj3', 'Eva Cavaldas');

INSERT  INTO t_post (post_title, post_content, post_extract, post_id_author, post_date_creation, post_status)
    VALUES ('Une nouvelle aventure', 'En route vers un nouveau départ! ... Qui sait où cela me m\'amènera.',
    'En route vers un nouveau départ!', 1, '2017/05/12 14:37:00', 'published');

INSERT  INTO t_post (post_title, post_content, post_extract, post_id_author, post_date_creation, post_status)
VALUES ('Lorem ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut hendrerit mauris ac porttitor
 accumsan. Nunc vitae pulvinar odio, auctor interdum dolor. Aenean sodales dui quis metus iaculis, hendrerit vulputate
  lorem vestibulum. Suspendisse pulvinar, purus at euismod semper, nulla orci pulvinar massa, ac placerat nisi urna eu
   tellus. Fusce dapibus rutrum diam et dictum. Sed tellus ipsum, ullamcorper at consectetur vitae, gravida vel sem.
   Vestibulum pellentesque tortor et elit posuere vulputate. Sed et volutpat nunc. Praesent nec accumsan nisi, in
   hendrerit nibh. In ipsum mi, fermentum et eleifend eget, eleifend vitae libero. Phasellus in magna tempor diam
   consequat posuere eu eget urna. Fusce varius nulla dolor, vel semper dui accumsan vitae. Sed eget risus neque.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut hendrerit mauris ac porttitor accumsan.
  Nunc vitae pulvinar odio, auctor interdum dolor. Aenean sodales dui ...', 1, '2017/05/12 14:37:00', 'published');

INSERT  INTO t_post (post_title, post_content, post_extract, post_id_author, post_date_creation, post_status)
VALUES ('Lorem ipsum en français', 'J’en dis autant de ceux qui, par mollesse d’esprit, c’est-à-dire par la crainte de la
peine et de la douleur, manquent aux devoirs de la vie. Et il est très facile de rendre raison de ce que j’avance. Car,
lorsque nous sommes tout à fait libres, et que rien ne nous empêche de faire ce qui peut nous donner le plus de plaisir,
 nous pouvons nous livrer entièrement à la volupté et chasser toute sorte de douleur ; mais, dans les temps destinés aux
  devoirs de la société ou à la nécessité des affaires, souvent il faut faire divorce avec la volupté, et ne se point
  refuser à la peine. La règle que suit en cela un homme sage, c’est de renoncer à de légères voluptés pour en avoir de
  plus grandes, et de savoir supporter des douleurs légères pour en éviter de plus fâcheuses.',
        'J’en dis autant de ceux qui, par mollesse d’esprit, c’est-à-dire par la crainte de la
peine et de la douleur, manquent aux devoirs de la vie. Et il est très facile ...', 1, now(), 'published');