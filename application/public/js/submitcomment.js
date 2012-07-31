$('#submitcomment').click(
			function()
			{
				$.post('/ryanthegreat/application/public/posts/' + $('#postid').val() + '/comment',
				       { poster: $('#poster').val(), comment: $('#comment').val() },
				       function(data)
				       {
						if(data['status'])
						{
							$('.success').css('display', 'block');
						}
						else if(data['error'] != false)
						{
							$('.error').css('display', 'block').html('<p>' + data['error'] + '</p>');
						}
				       },
				       'json');
			});