/*****************************************************************************
 *
 *                                    DATABASE 
 *
 *****************************************************************************/
CREATE DATABASE IF NOT EXISTS lightcms
  CHARACTER SET utf8
  COLLATE utf8_unicode_ci;
USE lightcms;


/*****************************************************************************
 *
 *                                    DATABASE USER
 *
 *****************************************************************************/
GRANT ALL PRIVILEGES ON lightcms.* TO 'lightcms_admin'@'localhost'
IDENTIFIED BY 'lightMyTutoWeb9';


/*****************************************************************************
 *
 *                                    DATABASE STRUCTURE
 *
 *****************************************************************************/


/************************************* BLOG *****************************/
DROP TABLE IF EXISTS t_blog;

CREATE TABLE t_blog (
  blo_id    BIGINT(10)  NOT NULL PRIMARY KEY AUTO_INCREMENT,
  blo_title VARCHAR(20) NOT NULL
)
  ENGINE = innodb
  CHARACTER SET utf8
  COLLATE utf8_unicode_ci;


/************************************* ADMIN *********************************/
DROP TABLE IF EXISTS t_admin;

CREATE TABLE t_admin (
  adm_id          BIGINT(10)   NOT NULL PRIMARY KEY AUTO_INCREMENT,
  adm_login       VARCHAR(10)  NOT NULL,
  adm_email       VARCHAR(80)  NOT NULL,
  adm_pwd         VARCHAR(80)  NOT NULL,
  adm_salt        VARCHAR(23)  NOT NULL,
  adm_web_name    VARCHAR(25)  NOT NULL,
  adm_role        VARCHAR(50)  NOT NULL,
  nb_notification TINYINT                           DEFAULT 0
)
  ENGINE = innodb
  CHARACTER SET utf8
  COLLATE utf8_unicode_ci;


/************************************* SUBSCRIBER *****************************/
DROP TABLE IF EXISTS t_subscriber;

CREATE TABLE t_subscriber (
  sub_id     BIGINT(10)  NOT NULL PRIMARY KEY AUTO_INCREMENT,
  sub_pseudo VARCHAR(10) NOT NULL,
  sub_email  VARCHAR(40) NOT NULL
)
  ENGINE = innodb
  CHARACTER SET utf8
  COLLATE utf8_unicode_ci;


/************************************* CATEGORIE ******************************/
DROP TABLE IF EXISTS t_categorie;

CREATE TABLE t_categorie (
  cat_id   BIGINT(10)  NOT NULL PRIMARY KEY AUTO_INCREMENT,
  cat_name VARCHAR(20) NOT NULL             DEFAULT 'default'
)
  ENGINE = innodb
  CHARACTER SET utf8
  COLLATE utf8_unicode_ci;


/************************************* POST ******************************/
DROP TABLE IF EXISTS t_post;

CREATE TABLE t_post (
  post_id                BIGINT(10)                                     NOT NULL PRIMARY KEY AUTO_INCREMENT,
  post_title             VARCHAR(100)                                   NOT NULL,
  post_id_author         BIGINT(10)                                     NOT NULL,
  post_content           VARCHAR(2000)                                  NOT NULL,
  post_extract           VARCHAR(1000)                                    NOT NULL,
  post_status            ENUM('not_published', 'published', 'disabled') NOT NULL             DEFAULT 'not_published',
  post_image             VARCHAR(100),
  post_nb_visit          BIGINT(20)                                     NOT NULL             DEFAULT 0,
  post_date_creation     DATETIME                                       NOT NULL,
  post_date_modification DATETIME                                       NULL,
  post_category_id       BIGINT(10)                                     NOT NULL,
  FOREIGN KEY (post_id_author) REFERENCES t_admin (adm_id),
  FOREIGN KEY (post_category_id) REFERENCES t_categorie (cat_id)
)
  ENGINE = innodb
  CHARACTER SET utf8
  COLLATE utf8_unicode_ci;


/************************************* COMMENT ******************************/
DROP TABLE IF EXISTS t_comment;

CREATE TABLE t_comment (
  com_id            BIGINT(10)                                                  NOT NULL PRIMARY KEY AUTO_INCREMENT,
  com_post_id      BIGINT(10)                                                  NOT NULL,
  com_parent_id     BIGINT(10),
  com_pseudo        VARCHAR(10)                                                 NOT NULL,
  com_email         VARCHAR(40)                                                 NOT NULL,
  com_status        ENUM('published', 'not_published', 'malicious', 'disabled') NOT NULL,
  com_date_creation DATETIME                                                    NOT NULL,
  com_message       TEXT                                                        NOT NULL,
  FOREIGN KEY (com_post_id) REFERENCES t_post (post_id),
  FOREIGN KEY (com_parent_id) REFERENCES t_comment (com_id)
)
  ENGINE = innodb
  CHARACTER SET utf8
  COLLATE utf8_unicode_ci;







