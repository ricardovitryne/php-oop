create table users
(
    iduser           int auto_increment primary key,
    email            varchar(100)  not null,
    name             varchar(100)  not null,
    password         varchar(100)  null,
    token            varchar(150)  null,
    token_expiration datetime      null
);
