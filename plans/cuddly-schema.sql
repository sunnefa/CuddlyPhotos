SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `cuddly` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `cuddly` ;

-- -----------------------------------------------------
-- Table `cuddly`.`core__settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__settings` (
  `setting_id` INT NOT NULL AUTO_INCREMENT ,
  `setting_name` VARCHAR(100) NOT NULL ,
  `setting_value` TEXT NULL ,
  PRIMARY KEY (`setting_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__pages` (
  `page_id` INT NOT NULL AUTO_INCREMENT ,
  `page_title` VARCHAR(100) NOT NULL ,
  `page_meta_description` VARCHAR(255) NULL ,
  `page_slug` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`page_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__modules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__modules` (
  `module_id` INT NOT NULL AUTO_INCREMENT ,
  `module_name` VARCHAR(100) NOT NULL ,
  `module_path` VARCHAR(100) NOT NULL ,
  `module_is_active` INT NULL ,
  PRIMARY KEY (`module_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__text`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__text` (
  `text_id` INT NOT NULL AUTO_INCREMENT ,
  `text_name` VARCHAR(100) NULL ,
  `text` TEXT NULL ,
  PRIMARY KEY (`text_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__pages_modules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__pages_modules` (
  `page_id` INT NOT NULL ,
  `module_id` INT NOT NULL ,
  `display_order` INT NOT NULL ,
  INDEX `page_id` (`page_id` ASC) ,
  INDEX `module_id` (`module_id` ASC) ,
  CONSTRAINT `page_id`
    FOREIGN KEY (`page_id` )
    REFERENCES `cuddly`.`core__pages` (`page_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `module_id`
    FOREIGN KEY (`module_id` )
    REFERENCES `cuddly`.`core__modules` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__text_pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__text_pages` (
  `text_id` INT NOT NULL ,
  `page_id` INT NOT NULL ,
  `display_order` INT NOT NULL ,
  INDEX `text_page_id` (`page_id` ASC) ,
  INDEX `page_text_id` (`text_id` ASC) ,
  CONSTRAINT `text_page_id`
    FOREIGN KEY (`page_id` )
    REFERENCES `cuddly`.`core__pages` (`page_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `page_text_id`
    FOREIGN KEY (`text_id` )
    REFERENCES `cuddly`.`core__text` (`text_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__users` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `user_first_name` VARCHAR(100) NOT NULL ,
  `user_last_name` VARCHAR(100) NOT NULL ,
  `user_email_address` VARCHAR(100) NOT NULL ,
  `user_password` VARCHAR(255) NOT NULL ,
  `user_registration_time` DATETIME NOT NULL ,
  `user_is_active` INT NULL ,
  `user_temp_token` VARCHAR(255) NULL ,
  `user_temp_token_expiry` DATETIME NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__privileges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__privileges` (
  `privilege_id` INT NOT NULL AUTO_INCREMENT ,
  `privilege_name` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`privilege_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`core__users_privileges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`core__users_privileges` (
  `privilege_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  INDEX `privilege_id` (`privilege_id` ASC) ,
  INDEX `user_id` (`user_id` ASC) ,
  CONSTRAINT `privilege_id`
    FOREIGN KEY (`privilege_id` )
    REFERENCES `cuddly`.`core__privileges` (`privilege_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `cuddly`.`core__users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`cuddly__categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`cuddly__categories` (
  `category_id` INT NOT NULL AUTO_INCREMENT ,
  `category_name` VARCHAR(100) NOT NULL ,
  `category_parent` INT NULL ,
  `category_description` TEXT NULL ,
  `category_created_by` INT NOT NULL ,
  `category_creation_date` DATETIME NOT NULL ,
  PRIMARY KEY (`category_id`) ,
  INDEX `category_user_id` (`category_created_by` ASC) ,
  INDEX `category_parent_id` (`category_parent` ASC) ,
  CONSTRAINT `category_user_id`
    FOREIGN KEY (`category_created_by` )
    REFERENCES `cuddly`.`core__users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `category_parent_id`
    FOREIGN KEY (`category_parent` )
    REFERENCES `cuddly`.`cuddly__categories` (`category_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`cuddly__albums`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`cuddly__albums` (
  `album_id` INT NOT NULL AUTO_INCREMENT ,
  `album_name` VARCHAR(100) NOT NULL ,
  `album_description` TEXT NULL ,
  `album_category` INT NULL ,
  `album_created_by` INT NOT NULL ,
  `album_creation_date` DATETIME NOT NULL ,
  `album_password` VARCHAR(255) NULL ,
  PRIMARY KEY (`album_id`) ,
  INDEX `category_id` (`album_category` ASC) ,
  INDEX `album_user_id` (`album_created_by` ASC) ,
  CONSTRAINT `category_id`
    FOREIGN KEY (`album_category` )
    REFERENCES `cuddly`.`cuddly__categories` (`category_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `album_user_id`
    FOREIGN KEY (`album_created_by` )
    REFERENCES `cuddly`.`core__users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuddly`.`cuddly__photos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cuddly`.`cuddly__photos` (
  `photo_id` INT NOT NULL AUTO_INCREMENT ,
  `photo_location` VARCHAR(255) NOT NULL ,
  `photo_album` INT NOT NULL ,
  `photo_description` TEXT NULL ,
  `photo_created_by` INT NOT NULL ,
  `photo_creation_date` DATETIME NOT NULL ,
  PRIMARY KEY (`photo_id`) ,
  INDEX `photo_album` (`photo_album` ASC) ,
  INDEX `photo_user` (`photo_created_by` ASC) ,
  CONSTRAINT `photo_album`
    FOREIGN KEY (`photo_album` )
    REFERENCES `cuddly`.`cuddly__albums` (`album_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `photo_user`
    FOREIGN KEY (`photo_created_by` )
    REFERENCES `cuddly`.`core__users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
