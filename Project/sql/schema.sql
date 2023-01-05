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
