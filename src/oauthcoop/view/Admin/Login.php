<!DOCTYPE html>
         <html>
         <head>

             <title>{! data('title') !}</title>
             <meta name="viewport" content="width=device-width, initial-scale=1">
             <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

             <style>
             </style>

         </head>

         <body class="" style="font-family:'sans-serif' !important;">

         <article class="" style="padding: 128px; margin-top: 150px; height: 100%;">
             <div style="display: flex; justify-content: center; color: grey; font-size: 64px;">
                 
                @foreach( data('feedback') as $key => $feedback)
                    {! $feedback->message."<br>" !}
                @endforeach

             </div>
         </article>

         <footer></footer>

         </body>

         </html>
         