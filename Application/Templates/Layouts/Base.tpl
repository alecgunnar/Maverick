<!Doctype html>
<html>
  <head>
    <title>{{ title }}</title>
    {% for file in cssFiles %}
    <link rel="stylesheet" type="text/css" href="{{ file }}" />
    {% endfor %}
  </head>
  <body>
    {{ body|raw }}
  </body>
</html>