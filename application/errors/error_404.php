<!DOCTYPE html>
<html lang="en">
<head>
<title>404 - Tactician</title>
<style type="text/css">
body {
	background-color: #fff;
	margin: 40px;
	font: 18px/20px normal Helvetica, Arial, sans-serif;
	color: #444;
}
a {
	color: #4285f4;
	background-color: transparent;
	font-weight: normal;
	text-decoration: none;
}
h1 {
	color: #000;
	background-color: transparent;
	font-size: 32px;
	font-weight: normal;
	margin: 0 0 10px 0;
	padding: 14px 15px 10px 15px;
}
h2 {
	color: #444;
	font-size: 24px;
	font-weight: normal;
	margin: 0 0 10px 0;
	padding: 14px 15px 10px 15px;
}
code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}
#container {
	margin: 10px;
}
p {
	margin: 12px 15px 12px 15px;
}
</style>
</head>
<body>
	<div id="container">
		<table>
			<tr>
				<td style="vertical-align: top;">
					<pre>
                /||\
                ||||
                ||||
                |||| /|\
           /|\  |||| |||
           |||  |||| |||
           |||  |||| |||
           |||  |||| d||
           |||  |||||||/
           ||b._||||~~'
           \||||||||
            `~~~||||
                ||||
                ||||
~~~~~~~~~~~~~~~~||||~~~~~~~~~~~~~~
  \/..__..--  . |||| \/  .  ..
\/         \/ \/    \/
        .  \/              \/    .
. \/             .   \/     .
   __...--..__..__       .     \/
\/  .   .    \/     \/    __..--..
					</pre>
				</td>
				<td style="vertical-align: top; padding-left:10px;">
					<h1>Tactician.com</h1>
					<h2><?php echo $heading; ?></h2>
					<p>
						Something went wrong:
					</p>
					<?php echo $message; ?>
					<p><a href="http://www.tacticiansoftware.com">Click here</a> to try again.</p>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>