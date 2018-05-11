
<?php
include("included_files/header.php");
include("included_files/handlers/edit_profile_handler.php");
?>
<div class="main_column_setting column">
	<h4>Change Account Settings</h4>
	<?php
	echo "<img src='" . $user['photoID'] ."' class='small_profile_pic'>";
	?>
	<!-- image source -->
	<br>
	<a href="upload.php">Upload new picture</a> <br><br><br>
	<br>

	<?php
	// edit the profile
	$user_data_query = mysqli_query($conn, "SELECT * FROM User WHERE userID='$loggedInUsedID'");
	$row = mysqli_fetch_array($user_data_query);

	$first_name = $row['firstName'];
	$last_name = $row['lastName'];
	$email = $row['userEmail'];
	$interests = $row['Interests'];
	$address = $row['Address'];
	?>
<div class="container">
	<form action="edit_profile.php" method="POST">
		<div class="form-group">
		    First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input"><br>
	    </div>
		<div class="form-group">
		    Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input"><br>
	    </div>
		<div class="form-group">
		   Email: <input type="text" name="email" value="<?php echo $email; ?>" id="settings_input"><br>
        </div>
				<div class="form-group">
				   interests: <input type="text" name="Interests" value="<?php echo $interests; ?>" id="settings_input"><br>
		        </div>
						<div class="form-group">
						   Address: <input type="text" name="address" value="<?php echo $address; ?>" id="settings_input"><br>
				        </div>

								<!-- add data -->
		<?php echo $message; ?>
        <div class="form-group">
		   <input type="submit" name="update_details" id="save_details" value="Update Details" class="info settings_submit"><br>
	    </div>
	</form>
</div>

<br>
<div class="container">
	<!-- change password details -->
	<h4>Change Password</h4>
	<form action="edit_profile.php" method="POST">
		<div class="form-group">
		    Old Password: <input type="password" name="old_password" id="settings_input"><br>
	    </div>
		<div class="form-group">
		    New Password: <input type="password" name="new_password_1" id="settings_input"><br>
		</div>
		<div class="form-group">
		    New Password Again: <input type="password" name="new_password_2" id="settings_input"><br>
        </div>
		<?php echo $password_message; ?>
        <div class="form-group">
		   <input type="submit" name="update_password" id="save_details" value="Update Password" class="info settings_submit"><br>
		</div>
	</form>
</div>
</div>
