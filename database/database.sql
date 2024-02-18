CREATE SCHEMA IF NOT EXISTS `files_upload` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `files_upload`.`users_infos` (
  `id_user` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(40) NOT NULL,
  `surnames` VARCHAR(225) NOT NULL,
  `nickname` VARCHAR(40),
  `email` VARCHAR(220) NOT NULL,
  `password` VARCHAR(220) NOT NULL,
  `path_picture` VARCHAR(100) NOT NULL DEFAULT '../files/users/default.jpg',
  PRIMARY KEY (`id_user`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE `files_upload`.`upload_infos` (
  `id_file` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT,
  `user_email` VARCHAR(220) NOT NULL,
  `path` VARCHAR(100) NOT NULL,
  `date_upload` DATETIME NOT NULL DEFAULT NOW(),
  `original_name` VARCHAR(220) NOT NULL,
  `new_name` VARCHAR(220) NOT NULL,
  `extension` VARCHAR(10) NOT NULL,
  `description` VARCHAR(35),
  `file_date` VARCHAR(10),
  PRIMARY KEY (`id_file`),
  INDEX `id_user_idx` (`id_user` ASC),
  CONSTRAINT `id_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `files_upload`.`users_infos` (`id_user`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);