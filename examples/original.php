<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<HTML>

<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">

<TITLE>G7-A Alarin</TITLE>
<SCRIPT LANGUAGE="JavaScript">
	function prepare_command(form, cmd_prefix) {
		var input_field = form.elements[2];
		var raw_value = input_field.value;
		var hidden_key = form.elements[0];

		hidden_key.value = cmd_prefix + ' ' + raw_value;
		return true;
	}
</SCRIPT>
</HEAD>

<BODY BGCOLOR="#C0C0C0" TEXT="#000000">
<CENTER>

<FORM>
<TABLE WIDTH="400" BGCOLOR="#E1E1E1" CELLPADDING="8" CELLSPACING="0" BORDER="0">
	<TR>
	<TD WIDTH="80" ALIGN="RIGHT">
	</TD>
	<TD ALIGN="CENTER">
		<FONT FACE="Times New Roman, Times, serif" SIZE="2"><?php echo $message; ?></FONT>
	</TD>
	<TD WIDTH="80" ALIGN="LEFT">
	</TD>
	</TR>
</TABLE>
</FORM>
<BR>

<FORM METHOD="GET" ACTION="index.php">
<TABLE WIDTH="400" BGCOLOR="#E1E1E1" CELLPADDING="8" CELLSPACING="0" BORDER="0">
	<TR>
	<TD WIDTH="80" ALIGN="RIGHT">
		<FONT FACE="Times New Roman, Times, serif" SIZE="2">apricot</FONT>
	</TD>
	<TD ALIGN="LEFT">
		<INPUT TYPE="hidden" NAME="key" VALUE="apricot">
		<INPUT TYPE="hidden" NAME="template" VALUE="original">
	</TD>
	<TD WIDTH="80" ALIGN="LEFT">
		<INPUT TYPE="submit" VALUE="Execute">
	</TD>
	</TR>
</TABLE>
</FORM>
<BR>

<FORM METHOD="GET" ACTION="index.php" ONSUBMIT="return prepare_command(this, 'ap add');">
<TABLE WIDTH="400" BGCOLOR="#E1E1E1" CELLPADDING="8" CELLSPACING="0" BORDER="0">
	<TR>
	<TD WIDTH="80" ALIGN="RIGHT">
		<FONT FACE="Times New Roman" SIZE="2">ap add</FONT>
	</TD>
	<TD ALIGN="LEFT">
		<INPUT TYPE="hidden" NAME="key" VALUE="">
		<INPUT TYPE="hidden" NAME="template" VALUE="original">
		<INPUT TYPE="text" NAME="value" SIZE="20">
	</TD>
	<TD WIDTH="80" ALIGN="LEFT">
		<INPUT TYPE="submit" VALUE="Execute">
	</TD>
	</TR>
</TABLE>
</FORM>
<BR>

<FORM METHOD="GET" ACTION="index.php" ONSUBMIT="return prepare_command(this, 'ap sub');">
<TABLE WIDTH="400" BGCOLOR="#E1E1E1" CELLPADDING="8" CELLSPACING="0" BORDER="0">
	<TR>
	<TD WIDTH="80" ALIGN="RIGHT">
		<FONT FACE="Times New Roman" SIZE="2">ap sub</FONT>
	</TD>
	<TD ALIGN="LEFT">
		<INPUT TYPE="hidden" NAME="key" VALUE="">
		<INPUT TYPE="hidden" NAME="template" VALUE="original">
		<INPUT TYPE="text" NAME="value" SIZE="20">
	</TD>
	<TD WIDTH="80" ALIGN="LEFT">
		<INPUT TYPE="submit" VALUE="Execute">
	</TD>
	</TR>
</TABLE>
</FORM>
<BR>

<FORM METHOD="GET" ACTION="index.php">
<TABLE WIDTH="400" BGCOLOR="#E1E1E1" CELLPADDING="8" CELLSPACING="0" BORDER="0">
	<TR>
	<TD WIDTH="80" ALIGN="RIGHT">
		<FONT FACE="Times New Roman" SIZE="2">remote</FONT>
	</TD>
	<TD ALIGN="LEFT">
		<INPUT TYPE="text" NAME="key" SIZE="20">
		<INPUT TYPE="hidden" NAME="template" VALUE="original">
	</TD>
	<TD WIDTH="80" ALIGN="LEFT">
		<INPUT TYPE="submit" VALUE="Execute">
	</TD>
	</TR>
</TABLE>
</FORM>

</CENTER>
</BODY>
</HTML>