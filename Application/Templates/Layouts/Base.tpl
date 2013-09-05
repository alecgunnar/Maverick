<!Doctype html>
<html>
  <head>
    <title>{{ title }}</title>
    {% for file in cssFiles %}
    <link rel="stylesheet" href="{{ file }}" />
    {% endfor %}
    {% for file in jsFiles %}
    <script src="{{ file }}"></script>
    {% endfor %}
  </head>
  <body>
    {{ body|raw }}
  </body>
</html>