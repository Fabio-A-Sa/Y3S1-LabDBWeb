create schema if not exists lbaw2255;

SET DateStyle TO European;

-----------------------------------------
-- Drop old schema
-----------------------------------------

DROP TABLE IF EXISTS group_join_request CASCADE;
DROP TABLE IF EXISTS comment_notification CASCADE;
DROP TABLE IF EXISTS user_notification CASCADE;
DROP TABLE IF EXISTS group_notification CASCADE;
DROP TABLE IF EXISTS post_notification CASCADE;
DROP TABLE IF EXISTS member CASCADE;
DROP TABLE IF EXISTS blocked CASCADE;
DROP TABLE IF EXISTS follow_request CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS post_likes CASCADE;
DROP TABLE IF EXISTS comment_likes CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS post CASCADE;
DROP TABLE IF EXISTS groups CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS configuration CASCADE;
DROP TABLE IF EXISTS message CASCADE;

DROP TYPE IF EXISTS user_notification_types;
DROP TYPE IF EXISTS group_notification_types;
DROP TYPE IF EXISTS post_notification_types;
DROP TYPE IF EXISTS comment_notification_types;

DROP FUNCTION IF EXISTS group_search_update CASCADE;
DROP FUNCTION IF EXISTS user_search_update CASCADE;
DROP FUNCTION IF EXISTS comment_search_update CASCADE;
DROP FUNCTION IF EXISTS post_search_update CASCADE;
DROP FUNCTION IF EXISTS verify_post_likes CASCADE;
DROP FUNCTION IF EXISTS verify_comment_likes CASCADE;
DROP FUNCTION IF EXISTS verify_group_post CASCADE;
DROP FUNCTION IF EXISTS verify_comment CASCADE;
DROP FUNCTION IF EXISTS group_owner CASCADE;
DROP FUNCTION IF EXISTS check_follow_request CASCADE;
DROP FUNCTION IF EXISTS verify_self_follow_req CASCADE;
DROP FUNCTION IF EXISTS check_group_join_req CASCADE;
DROP FUNCTION IF EXISTS verify_self_follow CASCADE;
DROP FUNCTION IF EXISTS delete_post_action CASCADE;
DROP FUNCTION IF EXISTS delete_comment_action CASCADE;
DROP FUNCTION IF EXISTS delete_group_action CASCADE;
DROP FUNCTION IF EXISTS delete_user_action CASCADE;
DROP FUNCTION IF EXISTS delete_mainnotification_action CASCADE;
DROP FUNCTION IF EXISTS configuration_action CASCADE;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE user_notification_types AS ENUM ('request_follow', 'started_following', 'accepted_follow');
CREATE TYPE group_notification_types AS ENUM ('requested_join', 'joined_group', 'accepted_join', 'leave_group', 'invite', 'ban', 'group_ownership');
CREATE TYPE post_notification_types AS ENUM ('liked_post', 'post_tagging');
CREATE TYPE comment_notification_types AS ENUM ('liked_comment', 'comment_post', 'reply_comment', 'comment_tagging');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE users (
   id SERIAL PRIMARY KEY,
   username VARCHAR(256) UNIQUE NOT NULL,
   password VARCHAR(256) NOT NULL,
   email VARCHAR(256) UNIQUE NOT NULL,
   name VARCHAR(256) NOT NULL,
   description VARCHAR(512),
   is_public BOOLEAN NOT NULL DEFAULT TRUE,
   remember_token VARCHAR(256) DEFAULT NULL
);

CREATE TABLE admin (
   id INTEGER PRIMARY KEY REFERENCES users (id) ON UPDATE CASCADE
);

CREATE TABLE groups (
   id SERIAL PRIMARY KEY,
   owner_id INTEGER NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
   name VARCHAR(256) UNIQUE NOT NULL,
   description VARCHAR(256),
   is_public BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE post (
   id SERIAL PRIMARY KEY,
   owner_id INTEGER NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
   group_id INTEGER REFERENCES groups (id) ON UPDATE CASCADE,
   is_public BOOLEAN NOT NULL DEFAULT TRUE,
   content VARCHAR(256),
   date TIMESTAMP NOT NULL CHECK (date <= now())
);

CREATE TABLE comment (
   id SERIAL PRIMARY KEY,
   owner_id INTEGER NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
   post_id INTEGER NOT NULL REFERENCES post (id) ON UPDATE CASCADE,
   previous INTEGER DEFAULT NULL REFERENCES comment (id) ON UPDATE CASCADE,
   content VARCHAR(256) NOT NULL,
   date TIMESTAMP NOT NULL CHECK (date <= now())
);

CREATE TABLE notification (
   id SERIAL PRIMARY KEY,
   date TIMESTAMP NOT NULL CHECK (date <= now()),
   notified_user INTEGER NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
   emitter_user INTEGER NOT NULL REFERENCES users (id) ON UPDATE CASCADE,
   viewed BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE comment_notification (
   id SERIAL PRIMARY KEY REFERENCES notification (id) ON UPDATE CASCADE,
   comment_id INTEGER NOT NULL REFERENCES comment (id) ON UPDATE CASCADE,
   notification_type comment_notification_types NOT NULL
);

CREATE TABLE user_notification (
   id INTEGER PRIMARY KEY REFERENCES notification (id) ON UPDATE CASCADE,
   notification_type user_notification_types NOT NULL
);

CREATE TABLE group_notification (
   id INTEGER PRIMARY KEY REFERENCES notification (id) ON UPDATE CASCADE,
   group_id INTEGER NOT NULL REFERENCES groups (id) ON UPDATE CASCADE,
   notification_type group_notification_types NOT NULL
);

CREATE TABLE post_notification (
   id INTEGER PRIMARY KEY REFERENCES notification (id) ON UPDATE CASCADE,
   post_id INTEGER NOT NULL REFERENCES post (id) ON UPDATE CASCADE,
   notification_type post_notification_types NOT NULL
);

CREATE TABLE member (
   user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   group_id INTEGER REFERENCES groups (id) ON UPDATE CASCADE,
   is_favorite BOOLEAN NOT NULL DEFAULT FALSE,
   PRIMARY KEY (user_id, group_id)
);

CREATE TABLE follow_request (
   req_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   rcv_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   PRIMARY KEY (req_id, rcv_id)
);

CREATE TABLE follows (
   follower_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   followed_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   PRIMARY KEY (follower_id, followed_id)
);

CREATE TABLE group_join_request (
   user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   group_id INTEGER REFERENCES groups (id) ON UPDATE CASCADE,
   PRIMARY KEY (user_id, group_id)
);

CREATE TABLE post_likes (
   user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   post_id INTEGER REFERENCES post (id) ON UPDATE CASCADE,
   PRIMARY KEY (user_id, post_id)
);

CREATE TABLE comment_likes (
   user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   comment_id INTEGER REFERENCES comment (id) ON UPDATE CASCADE,
   PRIMARY KEY (user_id, comment_id)
);

CREATE TABLE blocked (
   id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   PRIMARY kEY (id)
);

CREATE TABLE configuration (
   user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   notification_type VARCHAR(20) NOT NULL,
   active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE message (
   id SERIAL PRIMARY KEY,
   emitter_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   receiver_id INTEGER REFERENCES users (id) ON UPDATE CASCADE,
   content VARCHAR(256) NOT NULL,
   date TIMESTAMP NOT NULL CHECK (date <= now()),
   viewed BOOLEAN NOT NULL DEFAULT FALSE
);

-----------------------------------------
-- INDEXES
-----------------------------------------

CREATE INDEX notified_user_notification ON notification USING btree (notified_user);
CLUSTER notification USING notified_user_notification;

CREATE INDEX emitter_user_notification ON notification USING btree (emitter_user);
CLUSTER notification USING emitter_user_notification;

CREATE INDEX owner_id_post ON post USING hash (owner_id);

CREATE INDEX owner_id_comment ON comment USING hash (owner_id);

-----------------------------------------
-- FTS INDEXES
-----------------------------------------

-- Add column to group to store computed ts_vectors.
ALTER TABLE groups
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION group_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('simple', NEW.name), 'A') ||
         setweight(to_tsvector('simple', NEW.description), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('simple', NEW.name), 'A') ||
             setweight(to_tsvector('simple', NEW.description), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on group
CREATE TRIGGER group_search_update
 BEFORE INSERT OR UPDATE ON groups
 FOR EACH ROW
 EXECUTE PROCEDURE group_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_group ON groups USING GIN (tsvectors);

-- Add column to user to store computed ts_vectors.
ALTER TABLE users
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION user_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('simple', NEW.name), 'A') ||
         setweight(to_tsvector('simple', NEW.username), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.name <> OLD.name OR NEW.username <> OLD.username) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('simple', NEW.name), 'A') ||
             setweight(to_tsvector('simple', NEW.username), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on user
CREATE TRIGGER user_search_update
 BEFORE INSERT OR UPDATE ON users
 FOR EACH ROW
 EXECUTE PROCEDURE user_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_user ON users USING GIN (tsvectors);

-- Add column to post to store computed ts_vectors.
ALTER TABLE post
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION post_search_update() RETURNS TRIGGER AS $$
BEGIN
  IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = to_tsvector('simple', NEW.content);
  END IF;
  IF TG_OP = 'UPDATE' THEN
      IF (NEW.content <> OLD.content) THEN
           NEW.tsvectors = to_tsvector('simple', NEW.content);
      END IF;
 END IF;
   RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on post
CREATE TRIGGER post_search_update
 BEFORE INSERT OR UPDATE ON post
 FOR EACH ROW
 EXECUTE PROCEDURE post_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_post ON post USING GIN (tsvectors);

-- Add column to comment to store computed ts_vectors.
ALTER TABLE comment
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.
CREATE FUNCTION comment_search_update() RETURNS TRIGGER AS $$
BEGIN
  IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = to_tsvector('simple', NEW.content);
  END IF;
  IF TG_OP = 'UPDATE' THEN
      IF (NEW.content <> OLD.content) THEN
           NEW.tsvectors = to_tsvector('simple', NEW.content);
      END IF;
 END IF;
   RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create a trigger before insert or update on comments
CREATE TRIGGER comment_search_update
 BEFORE INSERT OR UPDATE ON comment
 FOR EACH ROW
 EXECUTE PROCEDURE comment_search_update();

-- Create a GIN index for ts_vectors.
CREATE INDEX search_comment ON comment USING GIN (tsvectors);

-----------------------------------------
-- TRIGGERS
-----------------------------------------

-- TRIGGER01
-- A user can only like a post once, or like posts from groups to which they belong or like comment in posts from public users or users they follow (business rule BR07)

CREATE FUNCTION verify_post_likes() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF EXISTS (SELECT * FROM post_likes WHERE NEW.user_id = user_id AND NEW.post_id = post_id) THEN
      RAISE EXCEPTION 'A user can only like a post once';
   END IF;
   IF EXISTS (SELECT * FROM post WHERE NEW.post_id = post.id AND post.group_id IS NOT NULL)
      AND NOT EXISTS (SELECT * FROM post,member WHERE NEW.post_id = post.id AND post.group_id = member.group_id
                  AND NEW.user_id = member.user_id) THEN
      RAISE EXCEPTION 'A user can only like posts from groups to which they belong';
   END IF;
   IF EXISTS (SELECT * FROM users,post WHERE NEW.post_id = post.id AND post.owner_id = users.id AND NOT users.is_public AND post.group_id IS NULL AND NEW.user_id <> post.owner_id)
      AND NOT EXISTS (SELECT * FROM post,follows WHERE NEW.post_id = post.id AND NEW.user_id = follows.follower_id AND follows.followed_id = post.owner_id) THEN
      RAISE EXCEPTION 'A user can only like posts from public users or users they follow';
   END IF;
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_post_likes
   BEFORE INSERT OR UPDATE ON post_likes
   FOR EACH ROW
   EXECUTE PROCEDURE verify_post_likes();

-- TRIGGER02
-- A user can only like a comment once (business rule BR08)

CREATE FUNCTION verify_comment_likes() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF EXISTS
      (SELECT * FROM comment_likes WHERE NEW.user_id = user_id AND NEW.comment_id = comment_id)
      THEN RAISE EXCEPTION 'A user can only like a comment once';
   END IF;

   IF EXISTS
      (SELECT * FROM post,comment WHERE NEW.comment_id = comment.id AND comment.post_id = post.id AND post.group_id IS NOT NULL)
   AND NOT EXISTS
      (SELECT * FROM post,member,comment WHERE NEW.comment_id = comment.id AND comment.post_id = post.id AND post.group_id = member.group_id AND NEW.user_id = member.user_id)
   THEN RAISE EXCEPTION 'Can not like a comment of a post of a group you do not belong to';
   END IF;

   IF EXISTS
      (SELECT * FROM users,post,comment WHERE NEW.comment_id = comment.id AND comment.post_id = post.id AND post.owner_id = users.id AND NOT users.is_public AND post.group_id IS NULL AND comment.owner_id <> post.owner_id)
   AND NOT EXISTS
      (SELECT * FROM post,follows,comment WHERE NEW.comment_id = comment.id AND comment.post_id = post.id AND NEW.user_id = follows.follower_id AND follows.followed_id = post.owner_id)
   THEN RAISE EXCEPTION 'Can not like comments in posts from private users you do not follow';
   END IF;

   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_comment_likes
   BEFORE INSERT OR UPDATE ON comment_likes
   FOR EACH ROW
   EXECUTE PROCEDURE verify_comment_likes();

-- TRIGGER03
-- A user can only post to a group that they belong to (business rule BR09)

CREATE FUNCTION verify_group_post() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF NOT EXISTS (SELECT * FROM member WHERE NEW.owner_id = user_id AND NEW.group_id = group_id)
      AND NEW.group_id IS NOT NULL THEN
         RAISE EXCEPTION 'A user can only post to a group that they belong to';
   END IF;
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_group_post
   BEFORE INSERT OR UPDATE ON post
   FOR EACH ROW
   EXECUTE PROCEDURE verify_group_post();

-- TRIGGER04
-- A user cannot follow themselves (business rule BR10)

CREATE FUNCTION verify_self_follow() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF NEW.follower_id = NEW.followed_id THEN
         RAISE EXCEPTION 'A user can not follow themselves';
   END IF;
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_self_follow
   BEFORE INSERT OR UPDATE ON follows
   FOR EACH ROW
   EXECUTE PROCEDURE verify_self_follow();

-- TRIGGER05
-- A user can only comment on posts from public users, posts from users they follow or on posts from groups to which they belong (business rule BR12)

CREATE FUNCTION verify_comment() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF EXISTS (SELECT * FROM post WHERE NEW.post_id = post.id AND post.group_id IS NOT NULL)
      AND NOT EXISTS (SELECT * FROM post,member WHERE NEW.post_id = post.id AND post.group_id = member.group_id
                  AND NEW.owner_id = member.user_id) THEN
      RAISE EXCEPTION 'A user can only comment on posts from groups to which they belong';
   END IF;
   IF EXISTS (SELECT * FROM users,post WHERE NEW.post_id = post.id AND post.owner_id = users.id AND NOT users.is_public AND post.group_id IS NULL AND NEW.owner_id <> post.owner_id)
      AND NOT EXISTS (SELECT * FROM post,follows WHERE NEW.post_id = post.id AND NEW.owner_id = follows.follower_id AND follows.followed_id = post.owner_id)
      THEN RAISE EXCEPTION 'A user can only comment posts from public users or users they follow';
   END IF;
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_comment
   BEFORE INSERT OR UPDATE ON comment
   FOR EACH ROW
   EXECUTE PROCEDURE verify_comment();

-- TRIGGER06
-- A group owner is also a member of your group (business rule BR13)

CREATE FUNCTION group_owner() RETURNS TRIGGER AS
$BODY$
BEGIN
   INSERT INTO member (user_id, group_id, is_favorite) VALUES (NEW.owner_id, NEW.id, True);
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER group_owner
   AFTER INSERT ON groups
   FOR EACH ROW
   EXECUTE PROCEDURE group_owner();

-- TRIGGER07
-- A user cannot request to follow a user that he/she already follow (business rule BR14)

CREATE FUNCTION check_follow_request() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF EXISTS
      (SELECT * FROM follows WHERE NEW.req_id = follower_id AND NEW.rcv_id = followed_id)
      THEN RAISE EXCEPTION 'Can not make a follow request to someone you already follow';
   END IF;
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_follow_request
   BEFORE INSERT ON follow_request
   FOR EACH ROW
   EXECUTE PROCEDURE check_follow_request();

-- TRIGGER08
-- A user cannot request to follow themselves (business rule BR15)

CREATE FUNCTION verify_self_follow_req() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF NEW.req_id = NEW.rcv_id THEN
         RAISE EXCEPTION 'A user can not request to follow themselves';
   END IF;
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verify_self_follow_req
   BEFORE INSERT OR UPDATE ON follow_request
   FOR EACH ROW
   EXECUTE PROCEDURE verify_self_follow_req();

-- TRIGGER09
-- A user cannot request to join a group that he/she is already a part of (business rule BR16)

CREATE FUNCTION check_group_join_req() RETURNS TRIGGER AS
$BODY$
BEGIN
   IF EXISTS
      (SELECT * FROM member WHERE NEW.user_id = user_id AND NEW.group_id = group_id)
      THEN RAISE EXCEPTION 'Can not request to join a group you are already a part of';
   END IF;
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_group_join_req
   BEFORE INSERT ON group_join_request
   FOR EACH ROW
   EXECUTE PROCEDURE check_group_join_req();

-- TRIGGER10
-- When deleting a post it also deletes its comments, subcomments, likes and notifications (business rule BR17)

CREATE FUNCTION delete_post_action() RETURNS TRIGGER AS
$BODY$
BEGIN
   DELETE FROM post_likes WHERE OLD.id = post_likes.post_id;
   DELETE FROM post_notification WHERE OLD.id = post_notification.post_id;
   DELETE FROM comment WHERE OLD.id = comment.post_id;
   RETURN OLD;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER delete_post_action
   BEFORE DELETE ON post
   FOR EACH ROW
   EXECUTE PROCEDURE delete_post_action();

-- TRIGGER11
-- When deleting a comment it also deletes its likes, subcomments and notifications (business rule BR18)

CREATE FUNCTION delete_comment_action() RETURNS TRIGGER AS
$BODY$
BEGIN
   DELETE FROM comment_likes WHERE OLD.id = comment_likes.comment_id;
   DELETE FROM comment_notification WHERE OLD.id = comment_notification.comment_id;
   DELETE FROM comment WHERE OLD.id = comment.previous;
   RETURN OLD;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER delete_comment_action
   BEFORE DELETE ON comment
   FOR EACH ROW
   EXECUTE PROCEDURE delete_comment_action();

-- TRIGGER12
-- When deleting a group it also deletes its posts, members, likes, comments, subcomments, notifications and group_notifications  (business rule BR19)

CREATE FUNCTION delete_group_action() RETURNS TRIGGER AS
$BODY$
BEGIN
   DELETE FROM post WHERE OLD.id = post.group_id;
   DELETE FROM member WHERE OLD.id = member.group_id;
   DELETE FROM group_join_request WHERE OLD.id = group_join_request.group_id;
   DELETE FROM group_notification WHERE OLD.id = group_notification.group_id;
   RETURN OLD;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER delete_group_action
   BEFORE DELETE ON groups
   FOR EACH ROW
   EXECUTE PROCEDURE delete_group_action();

-- TRIGGER13
-- After deleting a subnotification, delete main notification table entry

CREATE FUNCTION delete_mainnotification_action() RETURNS TRIGGER AS
$BODY$
BEGIN
   DELETE FROM notification WHERE OLD.id = notification.id;
   RETURN OLD;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER delete_main_post_notification_action
   AFTER DELETE ON post_notification
   FOR EACH ROW
   EXECUTE PROCEDURE delete_mainnotification_action();

CREATE TRIGGER delete_main_comment_notification_action
   AFTER DELETE ON comment_notification
   FOR EACH ROW
   EXECUTE PROCEDURE delete_mainnotification_action();

CREATE TRIGGER delete_main_group_notification_action
   AFTER DELETE ON group_notification
   FOR EACH ROW
   EXECUTE PROCEDURE delete_mainnotification_action();

CREATE TRIGGER delete_main_user_notification_action
   AFTER DELETE ON user_notification
   FOR EACH ROW
   EXECUTE PROCEDURE delete_mainnotification_action();

-- TRIGGER14
-- When new user appears, he initially gets all kinds of notification (business rule BR20)

CREATE FUNCTION configuration_action() RETURNS TRIGGER AS
$BODY$
BEGIN
   INSERT INTO configuration (user_id, notification_type) VALUES
      (NEW.id, 'request_follow'),
      (NEW.id, 'started_following'),
      (NEW.id, 'accepted_follow'),
      (NEW.id, 'requested_join'),
      (NEW.id, 'joined_group'),
      (NEW.id, 'accepted_join'),
      (NEW.id, 'leave_group'),
      (NEW.id, 'invite'),
      (NEW.id, 'ban'),
      (NEW.id, 'group_ownership'),
      (NEW.id, 'liked_post'),
      (NEW.id, 'post_tagging'),
      (NEW.id, 'liked_comment'),
      (NEW.id, 'comment_post'),
      (NEW.id, 'reply_comment'),
      (NEW.id, 'comment_tagging');
   RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER configuration_action
   AFTER INSERT ON users
   FOR EACH ROW
   EXECUTE PROCEDURE configuration_action();

-- Populate

INSERT INTO users (username, password, email, description, name, is_public) VALUES
    ('eduardanascimento', '$2a$12$1d1oU6vrjLpCuxqx3e8ihuJxJQIV8lZLnpPRYNIQnoIK.fq5ai44a', 'eduardanascimento@gmail.com', 'Ola sou a Eduarda. Admin do Only Feup. Qualquer dúvida dm me.', 'Eduarda Nascimento', TRUE),
    ('sneinhz', '$2a$12$a4aOzfXYxuVpYp/lfgnJ8.6quo2jPvHf.4TTqh.ldqSNWS0LHskHu', 'up201800177@fe.up.pt', 'I am the eggman, they are the eggmen I am the walrus, goo-goo goojoo', 'Marc Ferreira', TRUE),
    ('iamaccosta', '$2a$12$1u7wgn1d35oBuecNAs5vjO3KpJF4Gjh2AD/5tmCwMkLkUSub1QsLC', 'up201905916@fe.up.pt', 'They dont know that we know they know we know', 'André Costa', FALSE),
    ('fabiosa', '$2a$12$KkGKF7W94uVf92diV4uRhuEYiKf0to/aA5w9Tuqam8WnCKeyq4Pdi', 'up202007658@fe.up.pt', 'O caminho é longo mas a derrota é certa', 'Fábio Sá', TRUE),
    ('lourenco', '$2a$12$5ly5lW9rx//ju2xPPiezw.G6FM/n1WRbXoPpC3dBUpiiFRjXx8C4q', 'up202004816@fe.up.pt', 'Boss makes a dollar, I make a dime. Thats why my algorithms run in exponential time.', 'Lourenc Goncal', TRUE),
    ('yasminteixeira', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'yasminteixeira@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Yasmin Teixeira', TRUE),
    ('pedrosalto', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'pedrosalto@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Pedro Salto', TRUE),
    ('joanaoliveira', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'joanaoliveira@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Joana Oliveira', TRUE),
    ('victoriamoreira', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'victoriamoreira@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Victoria Moreira', TRUE),
    ('mayaraborges', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'mayaraborges@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Mayara Borges', TRUE),
    ('eliasloureiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'eliasloureiro@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Elias Loureiro', TRUE),
    ('lucaribeiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'lucaribeiro@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Luca Ribeiro', TRUE),
    ('manelpinho', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'manelpinho@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Manel Pinho', TRUE),
    ('marcossantos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'marcossantos@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Marcos Santos', TRUE),
    ('gasparsimoes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'gasparsimoes@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Gaspar Simoes', TRUE),
    ('jaimeferreira', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'jaimeferreira@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Jaime Ferreira', TRUE),
    ('franciscasa', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'franciscasa@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Francisca Sa', TRUE),
    ('vitorianeto', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'vitorianeto@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Vitoria Neto', TRUE),
    ('matildegaspar', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'matildegaspar@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Matilde Gaspar', TRUE),
    ('matiascorreia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'matiascorreia@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Matias Correia', TRUE),
    ('naiararodrigues', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'naiararodrigues@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Naiara Rodrigues', TRUE),
    ('mateusneves', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'mateusneves@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Mateus Neves', TRUE),
    ('josemaia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'josemaia@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Jose Maia', TRUE),
    ('andrefernandes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'andrefernandes@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Andre Fernandes', TRUE),
    ('isacfigueiredo', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'isacfigueiredo@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Isac Figueiredo', TRUE),
    ('barbaramaia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'barbaramaia@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Barbara Maia', TRUE),
    ('leonorvaz', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'leonorvaz@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Leonor Vaz', TRUE),
    ('anaramos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'anaramos@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Ana Ramos', TRUE),
    ('fredericoanjos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'fredericoanjos@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Frederico Anjos', TRUE),
    ('biancaraposo', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'biancaraposo@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Bianca Raposo', TRUE),
    ('anitaguerreiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'anitaguerreiro@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Anita Guerreiro', TRUE),
    ('noaabreu', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'noaabreu@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Noa Abreu', TRUE),
    ('pedrosimoes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'pedrosimoes@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Pedro Simoes', TRUE),
    ('victoriasimoes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'victoriasimoes@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Victoria Simoes', TRUE),
    ('vascolourenco', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'vascolourenco@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Vasco Lourenco', TRUE),
    ('raqueltorres', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'raqueltorres@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Raquel Torres', TRUE),
    ('catarinaantunes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'catarinaantunes@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Catarina Antunes', TRUE),
    ('samirapinho', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'samirapinho@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Samira Pinho', TRUE),
    ('afonsosousa', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'afonsosousa@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Afonso Sousa', TRUE),
    ('sebastiaogaspar', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'sebastiaogaspar@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Sebastiao Gaspar', TRUE),
    ('teresarocha', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'teresarocha@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Teresa Rocha', TRUE),
    ('mayaranascimento', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'mayaranascimento@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Mayara Nascimento', TRUE),
    ('deboramadeira', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'deboramadeira@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Debora Madeira', TRUE),
    ('xavierreis', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'xavierreis@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Xavier Reis', TRUE),
    ('dilanferreira', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'dilanferreira@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Dilan Ferreira', TRUE),
    ('liaguerreiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'liaguerreiro@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lia Guerreiro', TRUE),
    ('marcosfigueiredo', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'marcosfigueiredo@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Marcos Figueiredo', TRUE),
    ('vascoaraujo', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'vascoaraujo@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Vasco Araujo', TRUE),
    ('raquelanjos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'raquelanjos@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Raquel Anjos', TRUE),
    ('alicemachado', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'alicemachado@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Alice Machado', TRUE),
    ('alicedomingues', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'alicedomingues@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Alice Domingues', TRUE),
    ('cristianopinto', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'cristianopinto@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Cristiano Pinto', TRUE),
    ('tatianaloureiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'tatianaloureiro@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Tatiana Loureiro', TRUE),
    ('ivoassuncao', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'ivoassuncao@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Ivo Assuncao', TRUE),
    ('danielcarneiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'danielcarneiro@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Daniel Carneiro', TRUE),
    ('biancaantunes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'biancaantunes@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Bianca Antunes', TRUE),
    ('saraaraujo', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'saraaraujo@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Sara Araujo', TRUE),
    ('evaanjos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'evaanjos@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Eva Anjos', TRUE),
    ('lararamos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'lararamos@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lara Ramos', TRUE),
    ('miguelcastro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'miguelcastro@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Miguel Castro', TRUE),
    ('nicoleamaral', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'nicoleamaral@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Nicole Amaral', TRUE),
    ('lunalourenco', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'lunalourenco@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Luna Lourenco', TRUE),
    ('joaopaiva', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'joaopaiva@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Joao Paiva', TRUE),
    ('caetanacorreia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'caetanacorreia@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Caetana Correia', TRUE),
    ('beneditanunes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'beneditanunes@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Benedita Nunes', TRUE),
    ('martaleite', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'martaleite@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Marta Leite', TRUE),
    ('tomasmaia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'tomasmaia@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Tomas Maia', TRUE),
    ('carlotapinheiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'carlotapinheiro@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Carlota Pinheiro', TRUE),
    ('ericmagalhaes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'ericmagalhaes@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Eric Magalhaes', TRUE),
    ('tomasbatista', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'tomasbatista@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Tomas Batista', TRUE),
    ('pedronunes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'pedronunes@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Pedro Nunes', TRUE),
    ('igorleal', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'igorleal@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Igor Leal', TRUE),
    ('hugocardoso', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'hugocardoso@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Hugo Cardoso', TRUE),
    ('rubenmiranda', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'rubenmiranda@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Ruben Miranda', TRUE),
    ('sofianunes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'sofianunes@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Sofia Nunes', TRUE),
    ('emanuelsa', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'emanuelsa@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Emanuel Sa', TRUE),
    ('beatrizpires', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'beatrizpires@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Beatriz Pires', TRUE),
    ('margaridapinho', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'margaridapinho@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Margarida Pinho', TRUE),
    ('ricardobarbosa', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'ricardobarbosa@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Ricardo Barbosa', TRUE),
    ('deboramoreira', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'deboramoreira@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Debora Moreira', TRUE),
    ('franciscacruz', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'franciscacruz@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Francisca Cruz', TRUE),
    ('leonardoborges', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'leonardoborges@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Leonardo Borges', TRUE),
    ('alicepacheco', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'alicepacheco@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Alice Pacheco', TRUE),
    ('davidneves', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'davidneves@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'David Neves', TRUE),
    ('vascomatias', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'vascomatias@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Vasco Matias', TRUE),
    ('rafaelbaptista', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'rafaelbaptista@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Rafael Baptista', TRUE),
    ('eduardobatista', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'eduardobatista@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Eduardo Batista', TRUE),
    ('lorenzocastro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'lorenzocastro@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lorenzo Castro', TRUE),
    ('lorenzotorres', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'lorenzotorres@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lorenzo Torres', TRUE),
    ('juliacunha', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'juliacunha@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Julia Cunha', TRUE),
    ('anitamagalhaes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'anitamagalhaes@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Anita Magalhaes', TRUE),
    ('kevinribeiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'kevinribeiro@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Kevin Ribeiro', TRUE),
    ('teresamachado', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'teresamachado@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Teresa Machado', TRUE),
    ('eduardoborges', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'eduardoborges@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Eduardo Borges', TRUE),
    ('noahmachado', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'noahmachado@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Noah Machado', TRUE),
    ('antoniofernandes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'antoniofernandes@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Antonio Fernandes', TRUE),
    ('kellyanjos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'kellyanjos@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Kelly Anjos', TRUE),
    ('tomasantunes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'tomasantunes@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Tomas Antunes', TRUE),
    ('mateuspires', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'mateuspires@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Mateus Pires', TRUE),
    ('beneditaribeiro', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'beneditaribeiro@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Benedita Ribeiro', TRUE),
    ('angeloandrade', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'angeloandrade@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Angelo Andrade', TRUE),
    ('lialopes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'lialopes@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lia Lopes', FALSE),
    ('juliaamorim', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'juliaamorim@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Julia Amorim', FALSE),
    ('samuelsimoes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'samuelsimoes@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Samuel Simoes', FALSE),
    ('liaantunes', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'liaantunes@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lia Antunes', FALSE),
    ('jaimeneto', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'jaimeneto@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Jaime Neto', FALSE),
    ('pilarpacheco', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'pilarpacheco@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Pilar Pacheco', FALSE),
    ('alexandramorais', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'alexandramorais@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Alexandra Morais', FALSE),
    ('anitamoura', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'anitamoura@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Anita Moura', FALSE),
    ('vitorabreu', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'vitorabreu@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Vitor Abreu', FALSE),
    ('arianaraposo', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'arianaraposo@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Ariana Raposo', FALSE),
    ('franciscocorreia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'franciscocorreia@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Francisco Correia', FALSE),
    ('franciscobatista', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'franciscobatista@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Francisco Batista', FALSE),
    ('caioabreu', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'caioabreu@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Caio Abreu', FALSE),
    ('soraiaabreu', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'soraiaabreu@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Soraia Abreu', FALSE),
    ('caiocunha', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'caiocunha@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Caio Cunha', FALSE),
    ('yarasantos', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'yarasantos@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Yara Santos', FALSE),
    ('danielgarcia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'danielgarcia@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Daniel Garcia', FALSE),
    ('liaraposo', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'liaraposo@outlook.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lia Raposo', FALSE),
    ('gabrielamaia', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'gabrielamaia@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Gabriela Maia', FALSE),
    ('laraborges', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'laraborges@gmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lara Borges', FALSE),
    ('danielvicente', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'danielvicente@fe.up.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Daniel Vicente', FALSE),
    ('isacpacheco', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'isacpacheco@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Isac Pacheco', FALSE),
    ('laravel', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'laravel@hotmail.com', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Lara Vel', FALSE),
    ('aureapinto', '$2a$12$TuEIj41QBhqJt2dWEvMH.ODnBHq741MiKzTek1HVcX22Ekh6lXEey', 'aureapinto@sapo.pt', 'We are no strangers to love. You know the rules and so do I. A full commitments what Im thinking of. You wouldnt get this from any other guy.', 'Aurea Pinto', FALSE);

INSERT INTO admin (id) VALUES
    (1),
    (2),
    (3),
    (4),
    (5);

INSERT INTO groups (owner_id, name, is_public, description) VALUES
    (1, 'LBAW G2255', True, 'Work group for lbaw'),
    (2, 'Projeto FEUP 22/23', False, 'Engenharia Social'),
    (3, 'FSI hackers newbies', False, 'Educational purpose only'),
    (4, 'Grill', True, 'Introduce to cooking'),
    (5, 'RCOM', True, 'Same project since 1999'),
    (124, 'BD T01G06', True, 'Conceptual Model'),
    (7, 'LCOM', False, 'MEMORY game in C'),
    (8, 'PROG 22/23', True, 'Please join the gitlab. You can find the link in the moodle.'),
    (9, 'ESOF 18/19', False, ''),
    (10, 'MEIC UC OPTIONALS', True, 'You can talk about your options here and discuss it with other members'),
    (11, 'Praxis Touris', False, 'Cascou'),
    (12, 'LBAW G2212', False, 'NewsPaper'),
    (13, 'OnlyFeup Comunity', True, 'You can find friends here.'),
    (14, 'Admins Only', False, 'Obey the Rules'),
    (15, 'The Best Group', False, 'Founded by André/Fábio/Lourenço/Marcos');

INSERT INTO member (user_id, group_id) VALUES
    (1	,   2),
    (1	,   3),
    (124	,   3),
    (1	,   4),
    (1	,   5),
    (1	,   6),
    (1	,   7),
    (1	,   8),
    (1	,   10),
    (1	,   11),
    (1	,   12),
    (1	,   13),
    (2	,   1),
    (3	,   2),
    (4	,   3),
    (5	,   3),
    (6	,   3),
    (7	,   3),
    (8	,   4),
    (9	,   5),
    (10	,   5),
    (11	,   5),
    (12	,   5),
    (12	,   6),
    (13	,   6),
    (14	,   5),
    (14	,   8),
    (15	,   8),
    (16	,   9),
    (17	,   11),
    (8	,   3),
    (9	,   3),
    (9	,   4),
    (10	,   4),
    (11	,   4),
    (11 ,   6),
    (13	,   5),
    (15	,   6),
    (16	,   6),
    (17	,   6),
    (18	,   6),
    (18	,   7),
    (18	,   8),
    (19	,   8),
    (20	,   8),
    (21	,   8),
    (22	,   9),
    (23	,   9),
    (24	,   9),
    (25	,   9),
    (25	,   10),
    (26	,   10),
    (26	,   11),
    (27	,   11),
    (28	,   11),
    (29	,   11),
    (29	,   12),
    (30	,   12),
    (31	,   12),
    (32	,   12),
    (33	,   5),
    (34	,   6),
    (35	,   13),
    (36	,   5),
    (37	,   4),
    (38	,   11),
    (39	,   4),
    (40	,   4),
    (41	,   4),
    (42	,   15),
    (43	,   2),
    (44	,   12),
    (45	,   1),
    (46	,   6),
    (47	,   4),
    (48	,   8),
    (49	,   15),
    (50	,   1),
    (51	,   7),
    (52	,   4),
    (53	,   13),
    (54	,   2),
    (55	,   14),
    (56	,   10),
    (57	,   6),
    (58	,   14),
    (59	,   13),
    (60	,   10),
    (61	,   15),
    (62	,   4),
    (63	,   13),
    (64	,   8),
    (13	,   7),
    (16	,   10),
    (17	,   12),
    (65	,   12),
    (15  ,   9);

INSERT INTO post (owner_id, group_id, content, date) VALUES
    (1,	   1,   'I like FEUP!',    '8/10/17 0:00'),
    (1,	   1,   'When is the PFL exam, does someone know???',    '4/5/19 0:00'),
    (1,	   1,   'Is moodle working for you?',    '29/10/16 0:00'),
    (1,	   1,	  'Dont talk about this subject pls, I hate it!!!!',    '1/8/20 0:00'),
    (1,	   1,	  'Do we have classes today?',    '8/11/20 0:00'),
    (2,	   1,	  'Vamos ao café @fabiosa, @lourenco?',    '2/11/16 0:00'),
    (3,	   2,	  'FEUP vs ISEP, this is going to be nice!',    '11/9/21 0:00'),
    (4,	   3,	  'Studying for RCOM right now... :(',    '17/4/17 0:00'),
    (5,	   3,	  '<a href="../user/4">@fabiosa</a> vamos ao chocolate?',    '10/9/19 0:00'),
    (6,	   3,	  'FEUP my beloved <3 <3 S2',    '28/6/19 0:00'),
    (7,	   3,	  'why is @FEUP not a thing?',    '30/4/19 0:00'),
    (8,	   3,	  'ah 127.0.0.1/24 sweet 127.0.0.1/24',    '3/5/22 0:00'),
    (9,	   3,	  '<a href="../home/search?query=#tires">#tired</a>, send help',    '20/9/17 0:00'),
    (9,	   3,	  'LBAW is my favorite subject <3. Everyone who does not agree should be banned!!',    '24/10/16 0:00'),
    (9,	   3,	  'Why did MIEIC have to end, <a href="../home/search?query=#sad">#sad</a> <a href="../home/search?query=#4ever">#4ever</a>',    '26/10/18 0:00'),
    (9,	   4,	  'Capybaras room is noice',    '23/4/20 0:00'),
    (10,    4,	  'What',    '13/7/17 0:00'),
    (12,    5,	  'Do we have classes today?',    '24/4/20 0:00'),
    (12,    5,	  '<3<3<3 <a href="../home/search?query=#LBAW">#LBAW</a> <3<3<3',    '7/9/16 0:00'),
    (13,    5,	  'Passo a maior parte do meu dia no localhost:8000',    '26/5/21 0:00'),
    (14,    5,	  'Eu gosto de <a href="../home/search?query=#RCOM">#RCOM</a>. Alguem para estudar comigo?',    '2/2/17 0:00'),
    (15,    6,	  '<a href="../user/124">@laravel</a>, my beloved <3',    '3/10/19 0:00'),
    (16,    6,	  'Onde se liga o TuxS3?',    '18/10/20 0:00'),
    (17,    6,	  'Fiquei sem cabos para <a href="../home/search?query=#RCOM">#RCOM</a> <a href="../home/search?query=#1111">#1111</a>',    '3/7/17 0:00'),
    (18,    6,	  'Era suposto ligar os Tuxs, não irem jogar SuperTux!',    '9/12/16 0:00'),
    (18,    6,	  'Era suposto ligar os Tuxs, não irem jogar SuperTux!',    '9/12/22 0:00'),
    (18,    7,	  'Do we have classes today?',    '26/10/16 0:00'),
    (18,    7,	  'Do we have classes tomorrow?',    '5/7/21 0:00'),
    (18,    7,	  'I just realized we have an exam tomorrow. <a href="../home/search?query=#1111">#1111</a>',    '24/3/22 0:00'),
    (18,    8,	   NULL,    '1/12/20 0:00'),
    (18,    8,	  'Had lovely time at <a href="../home/search?query=#FEUP">#FEUP</a> during <a href="../home/search?query=#eramus">#erasmus</a>',    '14/1/20 0:00'),
    (18,    8,	   NULL,    '21/2/18 0:00'),
    (18,    8,	  'Adicionar um printf(), o codigo quebra, git commit :D',    '12/2/20 0:00'),
    (19,    8,	   NULL,    '27/10/20 0:00'),
    (20,    8,	  'The exam was so weird, why was Google not blocked??',    '6/5/21 0:00'),
    (21,    8,	   NULL,    '18/7/20 0:00'),
    (22,    9,	  'pq eu nao tenho uma arroba? alguem? pls',    '24/5/20 0:00'),
    (23,    9,	   NULL,    '6/2/20 0:00'),
    (24,    9,	  'I have put some things in my github, go check it out',    '16/4/16 0:00'),
    (25,    9,	   NULL,    '18/3/21 0:00'),
    (25,    9,	  'Ja deram push das alterações?',    '6/12/17 0:00'),
    (25,    10,   NULL,    '6/11/18 0:00'),
    (25,    10,   'Em vez de passar o natal com a familia, passo o natal com o localhost:8000',    '10/8/17 0:00'),
    (25,    10,   NULL,    '14/11/20 0:00'),
    (25,    10,   'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',    '27/4/18 0:00'),
    (25,    10,   NULL,    '18/4/22 0:00'),
    (25,    10,   'Meu colega deu push direto na main. Agora isso está com merge conflicts',    '9/9/22 0:00'),
    (26,    10,   NULL,    '10/3/18 0:00'),
    (26,    11,   'Alguém sabe quando é o recurso de LBAW?',    '26/9/18 0:00'),
    (27,    11,   NULL,    '6/3/19 0:00'),
    (28,    11,   'Feliz ano novo, maltinha',    '5/3/16 0:00'),
    (28,    11,   NULL,    '16/8/21 0:00'),
    (29,    11,   'Passei o natal, mas não o pinguim <a href="../home/search?query=#RCOM">#RCOM</a> :(',    '17/1/16 0:00'),
    (29,    12,   NULL,    '12/8/18 0:00'),
    (29,    12,   'Siga gente, recurso de <a href="../home/search?query=#DA">#DA</a>',    '26/8/17 0:00'),
    (29,    12,   NULL,    '8/1/18 0:00'),
    (30,    12,   '<a href="../home/search?query=#FEUPAT12AM">#FEUPAT12AM</a>',    '25/11/20 0:00'),
    (31,    12,   NULL,    '23/3/17 0:00'),
    (32,    12,	'O meu codigo esta cheio de cheiros :(',    '27/10/17 0:00'),
    (33,    NULL, 'Why cant the penguin just go through???????? :(',    '27/5/18 0:00'),
    (33,    NULL, 'É feioso, by Restaldo.',                                                                         '12/10/19 0:00'),
    (33,    NULL, 'What even was this DA exam?',    '21/8/19 0:00'),
    (34,    NULL, '<a href="../home/search?query=#depressed">#depressed</a> after this last DA exam',    '10/6/18 0:00'),
    (34,    NULL, 'where is the second MEST proj? still waiting...',    '2/4/22 0:00'),
    (35,    NULL, 'O que estão a dizer? Eu não sei ingles :(',    '28/10/20 0:00'),
    (36,    NULL, 'Dizem que D.Sebastião vai voltar um dia com o enunciado do <a href="../home/search?query=#2projetoMEST">#2projetoMEST</a>',    '21/5/20 0:00'),
    (37,    NULL, 'Alguém tem as respostas do exercicio 5?',    '23/5/22 0:00'),
    (38,    NULL, 'Passei o natal, mas não o pinguim <a href="../home/search?query=#RCOM">#RCOM</a>:(',    '1/11/20 0:00'),
    (39,    NULL, 'Had lovely time at <a href="../home/search?query=#FEUP">#FEUP</a> during <a href="../home/search?query=#eramus">#erasmus</a>',    '11/7/21 0:00'),
    (40,    NULL, 'What is the email of the LEIC department?',    '28/3/21 0:00'),
    (41,    NULL, 'The exam was so weird, why was Google not blocked??',    '16/6/17 0:00'),
    (42,    NULL, 'DDOS, can we do this guys?',    '19/9/22 0:00'),
    (42,    NULL, 'Sabiam que o GL joga minecraft?',    '23/8/20 0:00'),
    (42,    NULL, 'I have put some things in my github, go check it out',    '8/10/20 0:00'),
    (42,    NULL, 'Alguem para estudar <a href="../home/search?query=#RCOM">#RCOM</a> amanha à tarde na <a href="../home/search?query=#biblio">#biblio</a>',    '6/1/18 0:00'),
    (42,    NULL, 'Here is my grades: <br> ESOF - 19 <br> SOPE - 20 <br> DA - 9.4',    '28/1/22 0:00'),
    (43,    NULL, 'Wait, pq a tuna esta a tocar Never gonna give u up?',    '25/6/18 0:00'),
    (43,    NULL, '<a href="../home/search?query=#FEUPAT12AM">#FEUPAT12AM</a>',    '5/9/18 0:00'),
    (44,    NULL, '"voces de informática é que adoram computadores. Voces são aquele tipo de pessoa que à noite em vez de ir às meninas (ou meninos, é claro), ficam no computador" #sad',    '22/12/18 0:00'),
    (44,    NULL, 'O software pirateado não é feito pela madre teresa de calcutá!',    '13/4/18 0:00'),
    (44,    NULL, 'Dizemos ao utilizador que tem X peças disponíveis, porque ele sabe quantas peças tem no tabuleiro. Depois é só fazer as contas. Good job',    '8/1/18 0:00'),
    (45,    NULL, 'Someone at FEUP rn? Is the wifi working for you?',    '29/6/20 0:00'),
    (46,    NULL, 'Tá calado, <a href="../user/4">@fabiosa</a>',    '5/1/20 0:00'),
    (47,    NULL, 'Ele foi a FMUP fazer <a href="../home/search?query=#RCOM">#RCOM</a> ;)',    '23/9/18 0:00'),
    (48,    NULL, 'Quem é que mora em Santiago do Cacém e depois vai mudar o mundo? Ninguém',    '23/1/20 0:00'),
    (48,    NULL, 'Passei o natal, mas não o pinguim <a href="../home/search?query=#RCOM">#RCOM</a> :(',    '17/3/19 0:00'),
    (49,    NULL, 'Wait, pq a tuna esta a tocar Never gonna give u up?',    '21/9/18 0:00'),
    (49,    NULL, 'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',    '27/10/20 0:00'),
    (49,    NULL, 'Isso é trivial mas vcs não estão preparados para essa conversa smh',    '4/12/19 0:00'),
    (50,    NULL, 'Dont worry, be happy - <a href="../home/search?query=#1111">#1111</a>',    '2/4/19 0:00'),
    (51,    NULL, 'Dizem que D.Sebastião vai voltar um dia com o enunciado do <a href="../home/search?query=#SegundoProjetoMEST">#SegundoProjetoMEST</a>',    '8/5/19 0:00'),
    (52,    NULL, NULL,    '3/6/21 0:00'),
    (53,    NULL, 'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',    '8/6/21 0:00'),
    (54,    NULL, NULL,    '13/5/22 0:00'),
    (55,    NULL, 'o gitlab está funcionar? eu não consigo fazer pull',    '15/5/18 0:00'),
    (56,    NULL, NULL,    '1/4/22 0:00'),
    (57,    NULL, 'Github é melhor que gitlab, pq não podemos usar ele??????',    '19/3/21 0:00'),
    (58,    NULL, NULL,    '28/3/18 0:00'),
    (59,    NULL, 'Tux é lindo S2',    '29/8/21 0:00'),
    (60,    NULL, NULL,    '6/2/16 0:00'),
    (61,    NULL, '<a href="../user/124">@laravel</a>, my beloved <3',    '2/2/17 0:00'),
    (62,    NULL, NULL,    '28/5/20 0:00'),
    (63,    NULL, 'Onde esta o enunciado do <a href="../home/search?query=#SegundoProjetoMEST">#SegundoProjetoMEST</a>, ja era suposto entregar o projeto ha 2 semanas!',    '4/9/16 0:00'),
    (64,    NULL, NULL,    '3/12/17 0:00'),
    (64,    NULL, 'Studying for #IADE :(',    '24/12/20 0:00'),
    (64,    NULL, NULL,    '11/7/17 0:00'),
    (64,    NULL, 'Feliz ano novo para quem não mencionar aquele professor!',    '1/1/16 0:10'),
    (65,    NULL, NULL,    '28/11/18 0:00'),
    (66,    NULL, 'Feliz ano novo, maltinha',    '31/12/15 02:34'),
    (67,    NULL, NULL,    '31/7/18 0:00'),
    (68,    NULL, 'Em vez de passar o natal com a familia, passo o natal com o localhost:8000',    '31/5/18 0:00'),
    (68,    NULL, NULL,    '19/11/18 0:00'),
    (68,    NULL, 'Mas tu não tás vacinado? Que eu saiba, a vacina tem 5G',    '24/12/19 0:00'),
    (69,    NULL, NULL,    '20/2/18 0:00'),
    (69,    NULL, 'LBAW e PFL é pra entregar até dia 2 às 23:59 certo?',    '27/12/22 14:53'),
    (69,    NULL, NULL,    '13/8/22 0:00'),
    (69,    NULL, 'Petição dos estudantes do Ensino Superior do Porto pelo reforço da Ação Social Escolar - CANTINAS l ALOJAMENTO https://peticaopublica.com/?pi=PT114509',    '13/12/17 0:00'),
    (69,    NULL, NULL,    '30/10/19 0:00'),
    (70,    NULL, 'se parece demasiado bom para ser verdade, é porque é <br> stay safe',    '7/11/21 0:00'),
    (70,    NULL, NULL,    '10/4/20 0:00'),
    (70,    NULL, 'Exames aos sabados, FEUP being jenius again <a href="../home/search?query=#innovative">#innovative</a>',    '19/4/18 0:00'),
    (70,    NULL, NULL,    '13/3/20 0:00'),
    (70,    NULL, 'My code is not working, can someone help: <br> while(true){return "it works":}',    '19/4/18 0:00'),
    (70,    NULL, NULL,    '13/3/20 0:00'),
    (70,    NULL, 'TUNA, just like the fish??',    '19/4/18 0:00'),
    (70,    NULL, 'TUNA é atum',    '19/4/22 0:00');

INSERT INTO comment (owner_id, post_id, content, date) VALUES
    (1,      1,        'Great stuff here. Good job',   '31/12/22 0:00'),
    (2,      3,        'Great stuff here. Good job',   '31/12/22 0:00'),
    (3,      7,        'Great stuff here. Good job',   '31/12/22 0:00'),
    (4,      8,        'Wow! Awsome work',   '31/12/22 0:00'),
    (5,      10,       'Wow! Awsome work',   '31/12/22 0:00'),
    (6,      11,       'Wow! Awsome work',   '31/12/22 0:00'),
    (7,      14,       'Wow! Awsome work',   '31/12/22 0:00'),
    (8,      17,       'Wow! Awsome work',   '31/12/22 0:00'),
    (9,      20,       'Wow! Awesome work',   '31/12/222 0:00'),
    (10,     20,       'Wow! Awsome work',   '31/12/22 0:00'),
    (11,     23,       'Can we just appreciate how much effort they put into these posts? ',   '31/12/22 0:00'),
    (12,     24,       'Feup is awesome. Project Feup rules!',   '31/12/22 0:00'),
    (13,     27,       'Feup is awesome. Project Feup rules!',   '31/12/22 0:00'),
    (14,     35,       'Feup is awesome. Project Feup rules!',   '31/12/22 0:00'),
    (15,     36,       'Feup is awesome. Project Feup rules!',   '31/12/22 0:00'),
    (16,     42,       'Feup is awesome. Project Feup rules!',   '31/12/22 0:00'),
    (17,     54,       'BD is complicated.. please I need help ',   '31/12/22 0:00'),
    (1,      96,       'BD is complicated.. please I need help ',   '31/12/22 0:00'),
    (1,      78,       'BD is complicated.. please I need help ',   '31/12/22 0:00'),
    (2,      114,      '<a href="../user/1">@eduardanascimento</a> come see this post!',   '31/12/22 0:00'),
    (3,      122,      '<a href="../user/1">@eduardanascimento</a> come see this post!',   '31/12/22 0:00'),
    (4,      64,       'Hello do you like to be my friend and have lunch with me on Feup',   '31/12/22 0:00'),
    (5,      65,       'Hello do you like to be my friend and have lunch with me on Feup',   '2/10/22 0:00'),
    (6,      67,       'Hello do you like to be my friend and have lunch with me on Feup',   '10/10/22 0:00'),
    (7,      115,      'Hello do you like to be my friend and have lunch with me on Feup',   '7/10/22 0:00'),
    (8,      83,       'Hello do you like to be my friend and have lunch with me on Feup',   '13/10/22 0:00'),
    (9,      100,      'Hello do you like to be my friend and have lunch with me on Feup',   '12/10/22 0:00'),
    (10,     100,      'Hello do you like to be my friend and have lunch with me on Feup',   '10/10/22 0:00'),
    (11,     109,      'Rede social muito intuitiva. Muito fixe!',   '16/10/22 0:00'),
    (12,     100,      'Rede social muito intuitiva. Muito fixe!',   '10/10/22 0:00'),
    (12,     77,       'Rede social muito intuitiva. Muito fixe!',   '8/10/22 0:00'),
    (13,     108,      'Rede social muito intuitiva. Muito fixe!',   '3/10/22 0:00'),
    (14,     124,      'Rede social muito intuitiva. Muito fixe!',   '4/10/22 0:00'),
    (15,     74,       '<a href="../user/3">@iamaccosta</a>, <a href="../user/4">@fabiosa</a>, <a href="../user/2">@sneinhz</a>, <a href="../user/5">@lourenco</a> this post should be ban!',   '4/10/22 0:00'),
    (16,     82,       '<a href="../user/3">@iamaccosta</a>, <a href="../user/4">@fabiosa</a>, <a href="../user/2">@sneinhz</a>, <a href="../user/5">@lourenco</a> this post should be ban!',   '14/10/22 0:00'),
    (17,     78,       '<a href="../user/3">@iamaccosta</a>, <a href="../user/4">@fabiosa</a>, <a href="../user/2">@sneinhz</a>, <a href="../user/5">@lourenco</a> this post should be ban!',   '15/10/22 0:00'),
    (18,     108,      '<a href="../user/3">@iamaccosta</a>, <a href="../user/4">@fabiosa</a>, <a href="../user/2">@sneinhz</a>, <a href="../user/5">@lourenco</a> this post should be ban!',   '18/10/22 0:00'),
    (19,     64,       'Quem quer criar grupo comigo?',   '7/10/22 0:00'),
    (20,     109,      'Quem quer criar grupo comigo?',   '13/10/22 0:00'),
    (21,     86,       'Quem quer criar grupo comigo?',   '8/10/22 0:00'),
    (22,     109,      'Quem quer criar grupo comigo?',   '12/10/22 0:00'),
    (23,     70,       'Quem quer criar grupo comigo?',   '14/10/22 0:00'),
    (24,     77,       'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',   '17/10/22 0:00'),
    (25,     76,       'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',   '8/10/22 0:00'),
    (26,     101,      'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',   '12/10/22 0:00'),
    (27,     81,       'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',   '12/10/22 0:00'),
    (28,     77,       'Hoje comi arroz de pato!',   '2/10/22 0:00'),
    (29,     102,      'Hoje comi arroz de pato!',   '16/10/22 0:00'),
    (30,     100,      'Hoje comi arroz de pato!',   '9/10/22 0:00'),
    (31,     120,      'Hoje comi arroz de pato!',   '5/10/22 0:00'),
    (32,     98,       'Hoje comi arroz de pato!',   '7/10/22 0:00'),
    (33,     117,      'Máquinas de café são ótimas!',   '12/10/22 0:00'),
    (34,     72,       'Máquinas de café são ótimas!',   '14/10/22 0:00'),
    (35,     85,       'Máquinas de café são ótimas!',   '3/10/22 0:00'),
    (36,     123,      'Máquinas de café são ótimas!',   '5/10/22 0:00'),
    (37,     74,       'Máquinas de café são ótimas!',   '14/10/22 0:00'),
    (38,     89,       'Máquinas de café são ótimas!',   '13/10/22 0:00'),
    (39,     77,       'Hoje faltei à aula, alguém me pode mandar os apontamentos de BD?',   '13/10/22 0:00'),
    (40,     102,      'Hoje faltei à aula, alguém me pode mandar os apontamentos de BD?',   '13/10/22 0:00'),
    (41,     64,       'Hoje faltei à aula, alguém me pode mandar os apontamentos de BD?',   '14/10/22 0:00'),
    (42,     78,       'Hoje faltei à aula, alguém me pode mandar os apontamentos de BD?',   '18/10/22 0:00'),
    (43,     125,      'Hoje faltei à aula, alguém me pode mandar os apontamentos de BD?',   '4/10/22 0:00'),
    (44,     86,       'Great profile picture mate!',   '18/10/22 0:00'),
    (45,     100,      'Great profile picture mate!',   '18/10/22 0:00'),
    (46,     105,      'Great profile picture mate!',   '3/10/22 0:00'),
    (47,     113,      'Great profile picture mate!',   '10/10/22 0:00'),
    (48,     118,      'Great profile picture mate!',   '7/10/22 0:00'),
    (49,     106,      'Great profile picture mate!',   '11/10/22 0:00'),
    (50,     116,      'Great profile picture mate!',   '5/10/22 0:00'),
    (51,     78,       'Olá a todos',   '4/10/22 0:00'),
    (52,     95,       'Olá a todos',   '11/10/22 0:00'),
    (53,     80,       'Olá a todos',   '9/10/22 0:00'),
    (54,     69,       'Olá a todos',   '3/10/22 0:00'),
    (55,     69,       'Olá a todos',   '9/10/22 0:00'),
    (56,     63,       'Olá a todos',   '17/10/22 0:00'),
    (57,     117,      'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',   '8/10/22 0:00'),
    (58,     96,       'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',   '17/10/22 0:00'),
    (59,     103,      'Never gonna give you up. Never gonna let you down. Never gonna run around and desert you',   '12/10/22 0:00'),
    (60,     97,       'Bom ano novo a todos!',   '6/10/22 0:00'),
    (61,     104,      'Bom ano novo a todos!',   '3/10/22 0:00'),
    (62,     80,       'Bom ano novo a todos!',   '14/10/22 0:00'),
    (63,     119,      'Bom ano novo a todos!',   '3/10/22 0:00'),
    (64,     75,       'Bom ano novo a todos!',   '16/10/22 0:00'),
    (65,     94,       'Como se utiliza o laravel?',   '15/10/22 0:00'),
    (66,     65,       'Como se utiliza o laravel?',   '12/10/22 0:00'),
    (66,     69,       'Como se utiliza o laravel?',   '14/10/22 0:00'),
    (66,     80,       'Como se utiliza o laravel?',   '11/10/22 0:00'),
    (66,     117,      'Como se utiliza o laravel?',   '15/10/22 0:00'),
    (67,     108,      'Good point there. Keep the good work',   '6/10/22 0:00'),
    (67,     72,       'Good point there. Keep the good work',   '16/10/22 0:00'),
    (67,     80,       'Good point there. Keep the good work',   '12/10/22 0:00'),
    (67,     107,      'Good point there. Keep the good work',   '18/10/22 0:00'),
    (67,     90,       'Good point there. Keep the good work',   '7/10/22 0:00'),
    (68,     104,      'Good point there. Keep the good work',   '10/10/22 0:00'),
    (69,     106,      'Good point there. Keep the good work',   '6/10/22 0:00'),
    (69,     64,       '<a href="../user/124">@laravel</a> anda ver isto!',   '14/10/22 0:00'),
    (69,     64,       '<a href="../user/124">@laravel</a> anda ver isto!',   '2/10/22 0:00'),
    (69,     63,       '<a href="../home/search?query=#roasted">#roasted</a>',   '5/10/22 0:00'),
    (69,     69,       '<a href="../home/search?query=#roasted">#roasted</a>',   '2/10/22 0:00');

INSERT INTO comment (owner_id, post_id, previous, content, date) VALUES
   (70, 119, 82, 'Um bom exemplo de resposta!', '2/10/22 0:00'),
   (70, 119, 82, 'Outro belo exemplo de resposta!', '2/10/22 0:00');

INSERT INTO follows (follower_id, followed_id) VALUES
    (25,    124),
    (77,   	34),
    (16,   	 3),
    (77,   	48),
    (19,   	25),
    (25,   	 7),
    (7,     21),
    (43,   	 8),
    (93,   	24),
    (69,   	78),
    (12,   	58),
    (1,     65),
    (47,   	91),
    (64,   	43),
    (77,   	43),
    (9,     56),
    (11,   	 3),
    (48,   	52),
    (59,   	46),
    (1,     50),
    (20,   	48),
    (88,   	99),
    (44,   	55),
    (86,   	 6),
    (84,   	70),
    (57,   	 9),
    (94,   	88),
    (23,   	61),
    (88,   	90),
    (11,   	69),
    (84,   	36),
    (24,   	28),
    (6,     42),
    (71,   	 4),
    (22,   	62),
    (70,   	23),
    (8,     16),
    (36,   	 7),
    (51,   	 8),
    (77,   	12),
    (10,    24),
    (22,   	70),
    (75,   	79),
    (33,   	46),
    (24,   	 3),
    (77,   	75),
    (40,   	83),
    (58,   	75),
    (54,   	74),
    (80,   	81),
    (36,   	71),
    (41,   	58),
    (39,   	68),
    (65,   	17),
    (25,   	31),
    (34,   	36),
    (83,   	50),
    (95,   	53),
    (68,   	44),
    (88,   	64),
    (12,   	22),
    (90,   	95),
    (29,   	90),
    (97,   	25),
    (31,   	67),
    (98,   	22),
    (26,   	96),
    (18,   	64),
    (60,   	42),
    (18,   	60),
    (10,   	54),
    (25,   	52),
    (61,   	90),
    (62,   	89),
    (59,   	82),
    (95,   	74),
    (18,   	21),
    (74,   	33),
    (6,     70),
    (95,   	56),
    (97,   	71),
    (59,   	49),
    (35,   	48),
    (63,   	77),
    (11,   	49),
    (27,   	47),
    (54,   	27),
    (76,   	49),
    (43,   	24),
    (75,   	 8),
    (98,   	 4),
    (56,   	78),
    (30,   	37),
    (14,   	19),
    (80,   	57),
    (14,   	37),
    (50,   	74),
    (43,   	 1),
    (93,   	39),
    (44,   	30),
    (67,   	51),
    (58,   	45),
    (49,   	90),
    (76,   	30),
    (17,   	31),
    (29,   	16),
    (87,   	12),
    (54,   	15),
    (14,   	87),
    (75,   	69),
    (38,   	74),
    (90,   	82),
    (78,   	81),
    (85,   	10),
    (31,   	 2),
    (11,   	67),
    (24,   	78),
    (1,     70),
    (29,   	23),
    (5,     76),
    (16,   	68),
    (54,   	61),
    (88,   	81),
    (78,   	17),
    (86,   	68),
    (70,   	35),
    (84,   	 7),
    (50,   	46),
    (4,     97),
    (58,   	44),
    (30,   	19),
    (93,   	27),
    (26,   	50),
    (55,   	91),
    (61,   	26),
    (89,   	28),
    (79,   	 8),
    (20,   	77),
    (60,   	 2),
    (11,   	15),
    (84,   	40),
    (10,    28),
    (72,   	31),
    (56,   	61),
    (56,   	53),
    (71,   	21),
    (53,   	78),
    (29,   	49),
    (7,     61),
    (75,   	12),
    (18,   	82),
    (97,   	76),
    (60,   	 5),
    (84,   	33),
    (53,   	75),
    (20,   	92),
    (25,   	78),
    (48,   	54),
    (70,   	30),
    (92,   	 6),
    (24,   	38),
    (82,   	72),
    (2,     74),
    (78,   	51),
    (79,   	16),
    (44,   	54),
    (65,   	57),
    (18,   	23),
    (8,     92),
    (83,   	39),
    (77,   	38),
    (54,   	53),
    (2,     64),
    (3,     12),
    (4,     94),
    (65,   	12),
    (60,   	92),
    (53,   	40),
    (62,   	37),
    (94,   	61),
    (66,   	72),
    (13,   	 7),
    (16,   	 5),
    (68,   	96),
    (55,   	35),
    (9,     81),
    (41,   	76),
    (99,   	93),
    (56,   	13),
    (2,     87),
    (84,   	58),
    (69,   	13),
    (32,   	56),
    (63,   	76),
    (6,     24),
    (30,   	64),
    (1,     41),
    (94,   	64),
    (89,   	 5),
    (73,   	92),
    (35,   	 9),
    (36,   	45),
    (65,   	 7),
    (8,     59),
    (49,   	10),
    (62,   	10),
    (49,   	93),
    (78,   	90),
    (56,   	67),
    (1,     25),
    (44,   	46),
    (91,   	 6),
    (23,   	84),
    (25,   	35),
    (18,   	 1),
    (73,   	43),
    (79,   	76),
    (84,   	44),
    (45,   	31),
    (63,   	85),
    (98,   	94),
    (15,   	41),
    (87,   	76),
    (24,   	51),
    (98,   	46),
    (24,   	 8),
    (4,     39),
    (74,   	55),
    (79,   	 5),
    (19,   	13),
    (65,   	74),
    (82,   	32),
    (75,   	38),
    (37,   	27),
    (87,   	29),
    (59,   	66),
    (1,     98),
    (75,   	 5),
    (29,   	89),
    (8,     57),
    (89,   	19),
    (58,   	 3),
    (97,   	12),
    (53,   	 7),
    (44,   	11),
    (3,     76),
    (58,   	59),
    (26,   	94),
    (8,      9),
    (86,   	43),
    (57,   	19),
    (52,   	48),
    (90,   	23),
    (72,   	17),
    (62,   	57),
    (12,   	81),
    (2,     84),
    (57,   	39),
    (72,   	59),
    (69,   	23),
    (27,   	35),
    (82,   	15),
    (44,   	26),
    (18,   	78),
    (51,   	34),
    (51,   	99),
    (40,   	 3),
    (39,   	26),
    (32,   	67),
    (47,   	36),
    (34,   	91),
    (30,   	25),
    (70,   	61),
    (4,     87),
    (12,   	97),
    (37,   	76),
    (33,   	43),
    (72,   	98),
    (55,   	38),
    (47,   	40),
    (45,   	57),
    (72,   	97),
    (61,   	97),
    (51,   	21),
    (72,   	79),
    (88,   	 1),
    (24,   	81),
    (61,   	70),
    (38,   	87),
    (71,   	29),
    (19,   	46),
    (85,   	81),
    (79,   	49),
    (68,   	 8),
    (45,   	98),
    (8,     56),
    (37,   	36),
    (39,   	24),
    (3,     99),
    (15,   	92),
    (24,   	42),
    (90,   	44),
    (54,   	 1),
    (58,   	77),
    (79,   	13),
    (60,   	56),
    (71,   	85),
    (77,   	68),
    (59,   	24),
    (65,   	 2),
    (91,   	30),
    (33,   	78),
    (19,   	79),
    (73,   	69),
    (81,   	55),
    (50,   	64),
    (25,   	96),
    (58,   	66),
    (23,   	37),
    (80,   	77),
    (25,   	63),
    (47,   	58),
    (64,   	98),
    (29,   	31),
    (24,   	25),
    (20,   	 6),
    (15,   	 8),
    (88,   	31),
    (59,   	18),
    (46,   	72),
    (66,   	80),
    (35,   	 8),
    (19,   	95),
    (4,     92),
    (68,   	13),
    (10,   	57),
    (53,   	47),
    (2,     51),
    (70,   	77),
    (8,     54),
    (89,   	41),
    (43,   	60),
    (48,   	64),
    (4,     40),
    (84,   	49),
    (57,   	86),
    (73,   	70),
    (40,   	50),
    (70,   	 9),
    (89,   	35),
    (17,   	23),
    (39,   	51),
    (82,   	50),
    (7,     78),
    (40,   	75),
    (70,   	51),
    (5,     51),
    (9,     65),
    (10,   	56),
    (13,   	71),
    (82,   	41),
    (93,   	34),
    (38,   	94),
    (64,   	61),
    (71,   	45),
    (19,   	49),
    (90,   	28),
    (22,   	 1),
    (67,   	89),
    (61,   	20),
    (17,   	26),
    (91,   	93),
    (52,   	36),
    (69,   	94),
    (10,   	92),
    (11,   	91),
    (96,   	24),
    (50,   	67),
    (61,   	 9),
    (93,   	60),
    (75,   	11),
    (78,   	68),
    (61,   	 7),
    (50,   	 2);


-- follow_request
INSERT INTO follow_request (req_id, rcv_id) VALUES
    (98,    116),
    (77,   	124),
    (76, 	116),
    (28, 	120),
    (62, 	115),
    (58, 	110),
    (98, 	109),
    (53, 	113),
    (49, 	113),
    (3,	    119),
    (14, 	112),
    (62, 	121),
    (30, 	121),
    (33, 	108),
    (67, 	117),
    (51, 	120),
    (78, 	121),
    (75, 	123),
    (39, 	114),
    (90, 	103),
    (91, 	123),
    (46, 	122),
    (39, 	107),
    (59, 	112),
    (82, 	122),
    (2,	    105),
    (30, 	103),
    (15, 	113),
    (75, 	114),
    (82, 	111),
    (10, 	123),
    (34, 	103),
    (38, 	102),
    (39, 	116),
    (46, 	118),
    (53, 	108),
    (88, 	101),
    (60, 	110),
    (60, 	103),
    (26, 	124),
    (71, 	106),
    (34, 	114),
    (55, 	107),
    (90, 	110),
    (63, 	111),
    (43, 	121),
    (30, 	102),
    (61, 	102),
    (46, 	110),
    (49, 	105),
    (57, 	118),
    (75, 	110),
    (48, 	104),
    (47, 	112),
    (56, 	114),
    (4,	    101),
    (83, 	121),
    (33, 	124),
    (1,	    122),
    (18, 	121),
    (63, 	114),
    (62, 	118),
    (51, 	111),
    (95, 	120),
    (53, 	110),
    (91, 	110),
    (81, 	119),
    (33, 	105),
    (74, 	114),
    (70, 	110),
    (58, 	103),
    (82, 	105),
    (74, 	112),
    (90, 	104),
    (96, 	109),
    (22, 	111),
    (38, 	124),
    (26, 	114),
    (61, 	101),
    (12, 	121),
    (13, 	109),
    (11, 	113),
    (100,	118),
    (31, 	109),
    (3,	    112),
    (82, 	115),
    (35, 	104),
    (36, 	101),
    (17, 	114),
    (71, 	119),
    (100,	124),
    (94, 	109),
    (74, 	103),
    (87, 	110),
    (4,	    115),
    (94, 	120),
    (83, 	107),
    (81, 	101),
    (66, 	111),
    (71, 	108),
    (96, 	105),
    (2,	    101),
    (73, 	117),
    (28, 	123),
    (80, 	112),
    (59, 	116),
    (35, 	113),
    (38, 	109),
    (82, 	120),
    (81, 	106),
    (9,	    117),
    (73, 	104),
    (44, 	122),
    (82, 	108),
    (99, 	115),
    (32, 	104),
    (10, 	114),
    (48, 	122),
    (79, 	102),
    (56, 	121),
    (92, 	123),
    (56, 	120),
    (40, 	109),
    (32, 	124),
    (6,	    106),
    (50, 	111),
    (61, 	110),
    (99, 	120),
    (29, 	118),
    (3,	    123),
    (75, 	116),
    (5,	    119),
    (97, 	115),
    (91, 	109),
    (93, 	124),
    (31, 	115),
    (18, 	108),
    (61, 	106),
    (49, 	119),
    (96, 	104),
    (8,	    113),
    (54, 	120),
    (17, 	120),
    (54, 	115),
    (79, 	118),
    (85, 	104),
    (97, 	120),
    (30, 	119),
    (3,	    102),
    (95, 	105),
    (71, 	101),
    (70, 	113),
    (79, 	114),
    (50, 	121),
    (93, 	110),
    (22, 	107),
    (32, 	103),
    (91, 	106),
    (69, 	119),
    (6,	    114),
    (93, 	113),
    (64, 	102),
    (50, 	119),
    (60, 	120),
    (40, 	122),
    (42, 	123),
    (66, 	123),
    (72, 	115),
    (55, 	116),
    (10, 	120),
    (80, 	106),
    (10, 	119),
    (45, 	117),
    (57, 	115),
    (54, 	121),
    (80, 	123),
    (18, 	114),
    (95, 	113),
    (88, 	111),
    (2,	    121),
    (98, 	122),
    (45, 	102),
    (31, 	112),
    (84, 	102),
    (59, 	101),
    (73, 	106),
    (4,	    110),
    (84, 	110),
    (37, 	104),
    (72, 	113),
    (40, 	105),
    (39, 	101),
    (100,	115),
    (50, 	106),
    (86, 	108),
    (35, 	121),
    (98, 	117),
    (16, 	109),
    (28, 	105),
    (78, 	104),
    (10, 	122),
    (25, 	116),
    (54, 	117),
    (15, 	118),
    (83, 	116),
    (73, 	111),
    (4,	    104),
    (43, 	112),
    (8,	    112),
    (52, 	114),
    (83, 	123),
    (49, 	109),
    (37, 	101),
    (80, 	110),
    (64, 	117),
    (54, 	113);

-- group_join_request
INSERT INTO group_join_request (user_id, group_id) VALUES
    (5,     1),
    (34,    2),
    (70,    3),
    (23,    3),
    (48,    3),
    (69,   15),
    (15,    4),
    (22,    5),
    (39,    5),
    (97,    5),
    (47,    5),
    (22,   13),
    (2,     6),
    (38,    5),
    (81,    8),
    (16,    8),
    (1,     9),
    (13,   11),
    (26,    3),
    (75,   12),
    (45,    4),
    (55,   12),
    (26,    4),
    (45,    6),
    (18,    5),
    (20,    6),
    (40,    6),
    (93,    6),
    (62,    6),
    (56,    7),
    (34,    8),
    (2,     8),
    (78,    8),
    (32,    8),
    (34,    9),
    (37,    9),
    (43,    9),
    (47,    9),
    (53,    4),
    (55,   15),
    (63,   12);

-- post_likes
INSERT INTO post_likes (user_id,post_id) VALUES
    (68,     68),
    (10,    119),
    (88,     68),
    (63,     75),
    (4,	     94),
    (59,     72),
    (86,     70),
    (84,     89),
    (89,    121),
    (21,    100),
    (31,     59),
    (94,     96),
    (89,     60),
    (53,    109),
    (63,     71),
    (73,     88),
    (56,     60),
    (85,     63),
    (15,     78),
    (17,     26),
    (4,	     73),
    (95,     64),
    (11,     86),
    (37,    106),
    (71,     84),
    (99,     78),
    (93,     88),
    (99,     68),
    (32,    103),
    (66,     66),
    (64,    123),
    (30,    109),
    (82,     82),
    (62,    114),
    (55,     91),
    (39,     74),
    (91,     83),
    (57,     79),
    (1,	     89),
    (27,     50),
    (18,     69),
    (26,     94),
    (52,	 88),
    (88,	 97),
    (97,	120),
    (17,	 96),
    (26,	 75),
    (3,	    104),
    (16,	120),
    (5,	     74),
    (58,	 73),
    (73,	 71),
    (75,	 81),
    (52,	 65),
    (82,	 98),
    (20,	 92),
    (12,	 87),
    (44,	 91),
    (54,	 90),
    (2,	    101),
    (15,	 85),
    (97,	 68),
    (57,	106),
    (59,	 77),
    (62,	110),
    (75,	 71),
    (79,	 71),
    (78,	114),
    (36,	 19),
    (26,	 62),
    (55,	 95),
    (44,	 77),
    (11,	 64),
    (68,	117),
    (65,	 78),
    (67,    121),
    (64,	 90),
    (69,	103),
    (66,	 91),
    (8,	    122),
    (41,	 88),
    (90,	 96),
    (58,	113),
    (25,	 97),
    (95,	 69),
    (54,	112),
    (29,	 73),
    (58,	 95),
    (70,	 79),
    (63,	 61),
    (90,	 65),
    (39,	108),
    (1,	     87),
    (2,	     63),
    (1,	    107),
    (44,	 74),
    (85,	 69),
    (75,	102),
    (30,	 97),
    (61,	 74),
    (3,	     87),
    (66,	116),
    (20,	 99),
    (85,	 99),
    (49,	 82),
    (99,	112),
    (8,	     95),
    (76,	 83),
    (59,	120),
    (8,	     66),
    (47,	 87),
    (78,	 60),
    (49,	 95),
    (4,	     91),
    (10,     70),
    (53,	 68),
    (16,	 26),
    (64,	110),
    (31,	 72),
    (49,	104),
    (75,	 68),
    (73,	113),
    (48,	124),
    (8,	     73),
    (84,	 92),
    (35,	 61),
    (86,	 85),
    (46,	 23),
    (10,    122),
    (95,	 61),
    (74,	 92),
    (26,	 46),
    (94,	 91);

-- comment_likes
INSERT INTO comment_likes (user_id, comment_id) VALUES
    (70,	22),
    (56,	50),
    (13,	58),
    (21,	41),
    (18,	49),
    (4,	    25),
    (91,	44),
    (33,	98),
    (73,	19),
    (76,	87),
    (91,	63),
    (83,	65),
    (100,	83),
    (73,	70),
    (37,	60),
    (81,	64),
    (85,	29),
    (23,	53),
    (18,	92),
    (69,	98),
    (10,	89),
    (97,	23),
    (57,	50),
    (86,	95),
    (2,	    84),
    (3,	    81),
    (2,	    71),
    (22,	29),
    (7,	    60),
    (89,	49),
    (30,	61),
    (27,	20),
    (32,	83),
    (96,	52),
    (100,	53),
    (75,	60),
    (49,	36),
    (75,	27),
    (32,	87),
    (7,	    39),
    (36,	48),
    (69,	97),
    (5,	    74),
    (74,	66),
    (63,	78),
    (62,	37),
    (48,	22),
    (34,	49),
    (72,	94),
    (27,	51),
    (75,	63),
    (38,	33),
    (15,	91),
    (64,	15),
    (99,	67),
    (27,	44),
    (20,	48),
    (30,	89),
    (44,	36),
    (86,	87),
    (83,	70),
    (41,	62),
    (3,	    38),
    (88,	86),
    (79,	81),
    (59,	80),
    (88,	28),
    (81,	39),
    (26,	19),
    (42,	39),
    (32,	57),
    (99,	21),
    (36,	22),
    (41,	75),
    (66,	18),
    (17,	24),
    (38,	23),
    (76,	58),
    (5,	    55),
    (28,	77),
    (50,	98),
    (55,	26),
    (58,	95),
    (92,	89),
    (16,	43),
    (70,	97),
    (35,	81),
    (39,	25),
    (17,	55),
    (78,	21),
    (77,    58),
    (97,    44),
    (41,    72),
    (22,    58),
    (10,    60),
    (64,    69),
    (17,    37),
    (9,     31),
    (34,    55),
    (24,    28),
    (94,    47),
    (10,    83),
    (58,    56),
    (73,    80),
    (19,    69),
    (88,    72),
    (62,    85),
    (59,    94),
    (1,     26),
    (5,     20),
    (53,    63),
    (7,     50),
    (4,     87),
    (16,    60),
    (55,    65),
    (33,    56),
    (97,    25),
    (93,    76),
    (49,    25),
    (95,    18),
    (28,    73),
    (59,    28),
    (78,    76),
    (5,     80),
    (91,    74),
    (3,     97),
    (9,     64),
    (29,    71),
    (66,    22),
    (15,    38),
    (100,   49),
    (99,    20),
    (85,    49),
    (43,    54),
    (22,    48),
    (35,    92),
    (63,    69),
    (32,    63),
    (46,    21),
    (46,    47),
    (32,    27),
    (95,    64),
    (44,    72),
    (31,    28),
    (28,    78),
    (3,     39),
    (98,    55),
    (30,    64),
    (92,    28),
    (91,    66),
    (97,    50),
    (88,    38),
    (53,    29),
    (31,    98),
    (40,    97),
    (17,    86),
    (74,    97),
    (52,    25),
    (31,    72),
    (61,    40),
    (53,    33),
    (31,    38),
    (12,    93),
    (16,    79),
    (44,    53),
    (64,    93),
    (2,     43),
    (59,    47),
    (50,    32),
    (77,    31);

INSERT INTO message (emitter_id, receiver_id, content, date) VALUES
   (4, 1, 'Uma simples mensagem de teste!' ,'29/10/16 0:00'),
   (3, 1, 'Uma simples mensagem de teste!' ,'29/10/16 0:00'),
   (3, 1, 'EU sou repetida!' ,'29/10/16 0:00'),
   (2, 1, 'Uma simples mensagem de teste!' ,'29/10/16 0:00'),
   (2, 1, 'E eu sou mais nova!' ,'29/10/22 0:00');
