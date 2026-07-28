<!DOCTYPE html>
<html>
<head>
<title>Calculadora</title>
</head>

<body>

<h2>Calculadora</h2>

<form action="calcula.php" method="get" style="display:flex; gap:10px; align-items:center;">

<input type="number" name="numero1" placeholder="numero1" style="width:150px;">

<select name="operacao">
<option value="+">+</option>
<option value="-">-</option>
<option value="*">×</option>
<option value="/">÷</option>
</select>

<input type="number" name="numero2" placeholder="numero2" style="width:150px;">

<input type="submit" value="Calcular">

</form>

</body>
</html>