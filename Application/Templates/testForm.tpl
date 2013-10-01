{% for f in fields %}
  {% if f.getLabel() %}
    <strong>{{ f.getLabel() }}:</strong>{% if f.getError() is not empty %} {{ f.getError() }}{% endif %}<br />
  {% endif %}
  {{ f.render()|raw }}<br />
  <br />
{% endfor %}