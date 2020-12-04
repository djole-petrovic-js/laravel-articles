Full stack developer test
```
Since the relies are also comments, they are combined in one table.
Reply is a comment that has a relation to its parent comment.
Also, since the logic for comments is the same for the News and for
the Blogs, they are also combined. A column belongs_to, identifies this relation.
```
Comments table
```
create table comments (
  id int(11) primary key auto_increment not null,
  name varchar(255) not null,
  email varchar(255) not null,
  content text not null,
  approved tinyint(1) default 0,
  approved_email_sent tinyint(1) default 0,
  belongs_to_id int(11) not null,
  belongs_to varchar(255) not null,
  comment_id int(11) default 0,
  created_at datetime not null,
  updated_at datetime not null
)
```