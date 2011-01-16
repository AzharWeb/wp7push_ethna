<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>Mpnstest</h1>
		<h2>Toast</h2>
		{form action='./' ethna_action='send_toast'}
		<dl>
			<dt>{form_name  name='user_id'}</dt>
			<dd>{form_input name='user_id'}</dd>
			<dt>{form_name  name='title'}</dt>
			<dd>{form_input name='title'}</dd>
			<dt>{form_name  name='message'}</dt>
			<dd>{form_input name='message'}</dd>
		</dl>
		{form_submit}
		{/form}
    </body>
</html>
