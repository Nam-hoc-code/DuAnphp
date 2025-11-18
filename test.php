<?php echo "HELLO WORLD <br>" ?>

<?php echo "<br>" ?>

<p style = "color: blue">

<?php echo "Bài tập 1: <br>" ?>

<?php $number = 10;
    if ($number % 5 == 0) {
        echo "Kết quả ",$number," / 5 = ", $number / 5,".<br>";
    }
    else {
        echo "Không chia hết cho 5.<br>";
    }
?> </p>

<?php echo "<br>" ?>

<p style = "color: red">

<?php echo "Bài tập 2: <br>" ?>

<?php $number = 3;
    if ($number % 2 == 0) {
        echo $number," là số chẵn. <br>";
    }
    else {
        echo $number," không phải là số chẵn.<br>";
    }
?>
</p>
<?php echo "<br>" ?>

<p style = "color: green">

<?php echo "Bài tập 3: <br>"; ?>

<?php $grade = 8;
    if ($grade >= 8) {
        echo "Học sinh giỏi. <br>";
    }
    else if ($grade >= 6) {
        echo "Học sinh khá. <br>";
    }
    else if ($grade >= 4) {
        echo "Học sinh trung bình";

    }
    else {
        echo "Học sinh yếu";
    }

?>
</p>

<img src="" alt="">