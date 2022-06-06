
-- 06/06/2022
-- Model: PAPIME Model    Version: 0.0.1

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema papime
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `papime` DEFAULT CHARACTER SET utf8 ;
USE `papime` ;

-- -----------------------------------------------------
-- Table `papime`.`admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`admin` (
  `id_admin` INT NOT NULL AUTO_INCREMENT,
  `user` VARCHAR(25) NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `last_name` VARCHAR(40) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id_admin`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`professors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`professors` (
  `id_professors` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `last_name` VARCHAR(40) NOT NULL,
  `id_admin_register` INT NOT NULL,
  PRIMARY KEY (`id_professors`),
  INDEX `fk_professors_admin_idx` (`id_admin_register` ASC) ,
  CONSTRAINT `fk_professors_admin`
    FOREIGN KEY (`id_admin_register`)
    REFERENCES `papime`.`admin` (`id_admin`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`sessions` (
  `id_sessions` INT NOT NULL AUTO_INCREMENT,
  `id_professor` INT NOT NULL,
  `num_questions` INT NOT NULL,
  `group` VARCHAR(10) NOT NULL,
  `link` VARCHAR(55) NOT NULL,
  `teaching_situation` VARCHAR(50) NOT NULL,
  `session_name` VARCHAR(50) NOT NULL,
  `expiration` DATE NOT NULL,
  PRIMARY KEY (`id_sessions`),
  INDEX `fk_sessions_professors1_idx` (`id_professor` ASC) ,
  CONSTRAINT `fk_sessions_professors1`
    FOREIGN KEY (`id_professor`)
    REFERENCES `papime`.`professors` (`id_professors`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`reports`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`reports` (
  `id_reports` INT NOT NULL AUTO_INCREMENT,
  `sessions_id_sessions` INT NOT NULL,
  PRIMARY KEY (`id_reports`),
  INDEX `fk_reports_sessions1_idx` (`sessions_id_sessions` ASC) ,
  CONSTRAINT `fk_reports_sessions1`
    FOREIGN KEY (`sessions_id_sessions`)
    REFERENCES `papime`.`sessions` (`id_sessions`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`student_report`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`student_report` (
  `id_student_report` INT NOT NULL AUTO_INCREMENT,
  `reports_id_reports` INT NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `last_name` VARCHAR(40) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_student_report`),
  INDEX `fk_student_report_reports1_idx` (`reports_id_reports` ASC) ,
  CONSTRAINT `fk_student_report_reports1`
    FOREIGN KEY (`reports_id_reports`)
    REFERENCES `papime`.`reports` (`id_reports`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`student_behavior`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`student_behavior` (
  `id_student_behavior` INT NOT NULL AUTO_INCREMENT,
  `id_student_report` INT NOT NULL,
  `correct_answers` INT NOT NULL,
  PRIMARY KEY (`id_student_behavior`),
  INDEX `fk_student_behavior_student_report1_idx` (`id_student_report` ASC) ,
  CONSTRAINT `fk_student_behavior_student_report1`
    FOREIGN KEY (`id_student_report`)
    REFERENCES `papime`.`student_report` (`id_student_report`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`questions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`questions` (
  `id_question` INT NOT NULL AUTO_INCREMENT,
  `question` VARCHAR(200) NOT NULL,
  `sessions_id_sessions` INT NOT NULL,
  PRIMARY KEY (`id_question`),
  INDEX `fk_questions_sessions1_idx` (`sessions_id_sessions` ASC) ,
  CONSTRAINT `fk_questions_sessions1`
    FOREIGN KEY (`sessions_id_sessions`)
    REFERENCES `papime`.`sessions` (`id_sessions`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`attempts_per_question`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`attempts_per_question` (
  `id_attempts_per_question` INT NOT NULL AUTO_INCREMENT,
  `id_student_behavior` INT NOT NULL,
  `num_attempts` INT NOT NULL,
  `questions_id_question` INT NOT NULL,
  PRIMARY KEY (`id_attempts_per_question`),
  INDEX `fk_attempts_per_question_student_behavior1_idx` (`id_student_behavior` ASC) ,
  INDEX `fk_attempts_per_question_questions1_idx` (`questions_id_question` ASC) ,
  CONSTRAINT `fk_attempts_per_question_student_behavior1`
    FOREIGN KEY (`id_student_behavior`)
    REFERENCES `papime`.`student_behavior` (`id_student_behavior`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_attempts_per_question_questions1`
    FOREIGN KEY (`questions_id_question`)
    REFERENCES `papime`.`questions` (`id_question`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `papime`.`answers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `papime`.`answers` (
  `id_answer` INT NOT NULL AUTO_INCREMENT,
  `questions_id_question` INT NOT NULL,
  `answer` VARCHAR(200) NOT NULL,
  `is_correct` TINYINT NOT NULL,
  PRIMARY KEY (`id_answer`),
  INDEX `fk_answers_questions1_idx` (`questions_id_question` ASC) ,
  CONSTRAINT `fk_answers_questions1`
    FOREIGN KEY (`questions_id_question`)
    REFERENCES `papime`.`questions` (`id_question`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
