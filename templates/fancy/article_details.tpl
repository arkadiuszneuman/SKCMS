<div class="item">
	<h3>{title}</h3>
	<p class="newsInfo">Dodał: {author}, dnia {date}</p>
	<hr>
	{content}
	<hr>
	<div id="comments">
	<h3>{commentsNumber} do "{title}"</h3>
	{comments}
	</div>
	<div id="commentForm">
	<h3>Skomentuj</h3>
	<form action="" method="POST">
		<fieldset>
			<label for="author">Autor:</label>
			<input type="text" name="author" value="{commentAuthor}" size="65" {readonly}/>
			<label for="note">Treść:</label>
			<textarea name="note" rows="20" cols="100"></textarea>
			<input type="hidden" name="user_id" value="{user_id}" />
			<input type="hidden" name="hash" value="{hash}" />
			<input type="submit" name="submit" value="Wyślij"  />
		</fieldset>
	</form>
	</div>
</div>
