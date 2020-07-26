<?php
    function count_atSigns( $email ){
        $count = 0;
        for( $i=0; $i<strlen($email); $i++ ){
            if( $email[$i] == '@' ){
                $count++;
            }
        }
        return $count;
    }

    function validatePos() {
        for($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['year'.$i]) ) continue;
          if ( ! isset($_POST['desc'.$i]) ) continue;
      
          $year = $_POST['year'.$i];
          $desc = $_POST['desc'.$i];
      
          if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
          }
      
          if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
          }
        }
        return true;
    }

    function validateEdu()
    {
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['edu_year' . $i])) continue;
            if (!isset($_POST['edu_school' . $i])) continue;
            if (
                strlen($_POST['edu_year' . $i]) < 1 ||
                strlen($_POST['edu_school' . $i]) < 1
            ) {
                return 'All fields must be completed';
            }

            if (!is_numeric($_POST['edu_year' . $i])) {
                return 'Position year must be numeric';
            }
        }
        return true;
    }


    function countEmail() {
        $count = 0;
        $em = $_POST['email'];
        for( $i=0; $i<strlen($em); $i++ ){
            if( $em[$i] == '@' ){
                $count++;
            }
        }
        return $count;
    }

    function loadPos($pdo, $profile_id) {
        $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
        $stmt->execute(array(":prof" => $profile_id));
        $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $positions;
    }
    
    function loadEdu($pdo, $profile_id) {
        $stmt = $pdo->prepare('SELECT * FROM Education 
                                JOIN Institution
                                ON Education.institution_id = Institution.institution_id
        WHERE profile_id = :prof ORDER BY rank');
        $stmt->execute(array(":prof" => $profile_id));
        $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $educations;
    }

    function insertPositions($pdo, $profile_id)
    {
        $rank = 1;
        for (
            $i = 0;
            $i <= 9;
            $i++
        ) {
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;
            $sql = "INSERT INTO Position (profile_id, rank, year, description)
                VALUES (:pid, :ra, :yr, :de)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':pid' => $profile_id,
                ':ra' => $rank,
                ':yr' => $_POST['year' . $i],
                ':de' => $_POST['desc' . $i],
            ]);
            $rank++;
        }
    }

    // TODO: update the function
    function insertEducations($pdo, $profile_id)
    {
        $rank = 1;
        for (
            $i = 0;
            $i <= 9;
            $i++
        ) {
            if (!isset($_POST['edu_year' . $i])) continue;
            if (!isset($_POST['edu_school' . $i])) continue;
            $year = $_POST['edu_year' . $i];
            $school = $_POST['edu_school' . $i];

            //look up the school if it is already there
            $institution_id = false;
            $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
            $stmt->execute([
                ':name' => $school
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) $institution_id = $row['institution_id'];

            // if there is no institution insert it
            if ($institution_id === false) {
                $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
                $stmt->execute([
                    ':name' => $school
                ]);
                $institution_id = $pdo->lastInsertId();
            }

            //insert into education 
            $stmt = $pdo->prepare("INSERT INTO Education (profile_id, rank, year, institution_id)
                VALUES (:pid, :ra, :yr, :iid)");
            $stmt->execute([
                ':pid' => $profile_id,
                ':ra' => $rank,
                ':yr' => $_POST['edu_year' . $i],
                ':iid' => $institution_id
            ]);
            $rank++;
        }
    }

?>