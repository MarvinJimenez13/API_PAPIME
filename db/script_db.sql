repository.checkColorGuardado(result ->{
    if(result.isSuccessfull()){
        checkColorFavorito.postValue(result.getData());
    }else{
        HttpException resultError = (HttpException) result.getError();
        if(resultError.code() == 401){
            if(count > 0){
                count--;
                FirebaseUtils firebase = new FirebaseUtils();
                if(firebase.existSession() &&
                        PreferencesUtils.getDataProfile(ColorBerelApp.getInstance()).getToken() != null
                        && AndroidUtils.isNetworkAvailable(ColorBerelApp.getInstance())
                        && PreferencesUtils.getDataProfile(ColorBerelApp.getInstance()).getToken() != ""){

                    FirebaseAuth.getInstance().getCurrentUser().getIdToken(true).addOnSuccessListener(task -> {
                        if(task.getToken() != PreferencesUtils.getDataProfile(ColorBerelApp.getInstance()).getToken()){
                            updateSessionToken(PreferencesUtils.getDataProfile(ColorBerelApp.getInstance()).getToken(), task.getToken(), ColorBerelApp.getInstance());
                            repository.updateSessionToken(resultUpdate -> {
                                if(resultUpdate.getData() != null){//guardamos nevo token
                                    if(resultUpdate.getData().getSuccess()){
                                        Log.d("logtoken", "EXITO: " + task.getToken());
                                        PreferencesUtils.saveNewToken(task.getToken(), ColorBerelApp.getInstance());
                                        checkColorGuardado(task.getToken(), idColor, layoutInflater);
                                    }else{
                                        count = 1;
                                        firebaseUtils.logout();
                                    }
                                }else{
                                    //Cerramos sesion
                                    count = 1;
                                    firebaseUtils.logout();
                                    Log.d("logtoken", "ERROR: " + result.getError().getMessage());
                                }
                            }, PreferencesUtils.getDataProfile(ColorBerelApp.getInstance()).getToken(), task.getToken());
                            Log.d("tagtoken", "\n Old: " + PreferencesUtils.getDataProfile(ColorBerelApp.getInstance()).getToken());
                            Log.d("tagtoken", "New: " + task.getToken());
                        }
                    });

                }
            }else{
                //cerrar sesion
                firebaseUtils.logout();
                count = 1;
            }
        }else{
            error.postValue(new DataException(result.getError()));
        }
    }

    loader.postValue(false);
}, token, idColor);


-- 08/07/2022
-- Model: comer208_PAPIME Model    Version: 0.1.0


SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema comer208_papime
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema comer208_papime
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `comer208_papime` DEFAULT CHARACTER SET utf8 ;
USE `comer208_papime` ;

-- -----------------------------------------------------
-- Table `comer208_papime`.`admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`admin` (
  `id_admin` INT NOT NULL AUTO_INCREMENT,
  `user` VARCHAR(25) NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `last_name` VARCHAR(40) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `token` VARCHAR(300) NULL,
  PRIMARY KEY (`id_admin`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`professors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`professors` (
  `id_professors` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `last_name` VARCHAR(40) NOT NULL,
  `id_admin_register` INT NOT NULL,
  `token` VARCHAR(300) NULL,
  PRIMARY KEY (`id_professors`),
  INDEX `fk_professors_admin_idx` (`id_admin_register` ASC),
  CONSTRAINT `fk_professors_admin`
    FOREIGN KEY (`id_admin_register`)
    REFERENCES `comer208_papime`.`admin` (`id_admin`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`sessions` (
  `id_sessions` INT NOT NULL AUTO_INCREMENT,
  `id_professor` INT NOT NULL,
  `group_num` VARCHAR(10) NOT NULL,
  `link` VARCHAR(55) NOT NULL,
  `teaching_situation` VARCHAR(50) NOT NULL,
  `session_name` VARCHAR(50) NOT NULL,
  `expiration` DATE NOT NULL,
  `code` VARCHAR(45) NOT NULL,
  `course` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_sessions`),
  INDEX `fk_sessions_professors1_idx` (`id_professor` ASC),
  CONSTRAINT `fk_sessions_professors1`
    FOREIGN KEY (`id_professor`)
    REFERENCES `comer208_papime`.`professors` (`id_professors`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`reports`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`reports` (
  `id_reports` INT NOT NULL AUTO_INCREMENT,
  `sessions_id_sessions` INT NOT NULL,
  PRIMARY KEY (`id_reports`),
  INDEX `fk_reports_sessions1_idx` (`sessions_id_sessions` ASC),
  CONSTRAINT `fk_reports_sessions1`
    FOREIGN KEY (`sessions_id_sessions`)
    REFERENCES `comer208_papime`.`sessions` (`id_sessions`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`student_report`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`student_report` (
  `id_student_report` INT NOT NULL AUTO_INCREMENT,
  `reports_id_reports` INT NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `last_name` VARCHAR(40) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_student_report`),
  INDEX `fk_student_report_reports1_idx` (`reports_id_reports` ASC),
  CONSTRAINT `fk_student_report_reports1`
    FOREIGN KEY (`reports_id_reports`)
    REFERENCES `comer208_papime`.`reports` (`id_reports`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`student_behavior`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`student_behavior` (
  `id_student_behavior` INT NOT NULL AUTO_INCREMENT,
  `id_student_report` INT NOT NULL,
  `correct_answers` INT NOT NULL,
  PRIMARY KEY (`id_student_behavior`),
  INDEX `fk_student_behavior_student_report1_idx` (`id_student_report` ASC),
  CONSTRAINT `fk_student_behavior_student_report1`
    FOREIGN KEY (`id_student_report`)
    REFERENCES `comer208_papime`.`student_report` (`id_student_report`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`rooms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`rooms` (
  `id_rooms` INT NOT NULL AUTO_INCREMENT,
  `num_questions` INT NOT NULL,
  `images` TINYINT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `sessions_id_sessions` INT NOT NULL,
  PRIMARY KEY (`id_rooms`),
  INDEX `fk_rooms_sessions1_idx` (`sessions_id_sessions` ASC),
  CONSTRAINT `fk_rooms_sessions1`
    FOREIGN KEY (`sessions_id_sessions`)
    REFERENCES `comer208_papime`.`sessions` (`id_sessions`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`questions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`questions` (
  `id_question` INT NOT NULL AUTO_INCREMENT,
  `question` VARCHAR(200) NOT NULL,
  `mul_option` TINYINT NOT NULL,
  `num_responses` INT NOT NULL,
  `rooms_id_rooms` INT NOT NULL,
  PRIMARY KEY (`id_question`),
  INDEX `fk_questions_rooms1_idx` (`rooms_id_rooms` ASC),
  CONSTRAINT `fk_questions_rooms1`
    FOREIGN KEY (`rooms_id_rooms`)
    REFERENCES `comer208_papime`.`rooms` (`id_rooms`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`attempts_per_question`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`attempts_per_question` (
  `id_attempts_per_question` INT NOT NULL AUTO_INCREMENT,
  `id_student_behavior` INT NOT NULL,
  `num_attempts` INT NOT NULL,
  `questions_id_question` INT NOT NULL,
  PRIMARY KEY (`id_attempts_per_question`),
  INDEX `fk_attempts_per_question_student_behavior1_idx` (`id_student_behavior` ASC),
  INDEX `fk_attempts_per_question_questions1_idx` (`questions_id_question` ASC),
  CONSTRAINT `fk_attempts_per_question_student_behavior1`
    FOREIGN KEY (`id_student_behavior`)
    REFERENCES `comer208_papime`.`student_behavior` (`id_student_behavior`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_attempts_per_question_questions1`
    FOREIGN KEY (`questions_id_question`)
    REFERENCES `comer208_papime`.`questions` (`id_question`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comer208_papime`.`answers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comer208_papime`.`answers` (
  `id_answer` INT NOT NULL AUTO_INCREMENT,
  `questions_id_question` INT NOT NULL,
  `answer` VARCHAR(200) NULL,
  `is_correct` TINYINT NOT NULL,
  PRIMARY KEY (`id_answer`),
  INDEX `fk_answers_questions1_idx` (`questions_id_question` ASC),
  CONSTRAINT `fk_answers_questions1`
    FOREIGN KEY (`questions_id_question`)
    REFERENCES `comer208_papime`.`questions` (`id_question`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
