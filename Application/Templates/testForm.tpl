{% for f in fields %}
  {% if f.getTpl() is empty %}
    {% if f.getLabel() %}
    <strong>{{ f.getLabel() }}:</strong>{% if f.getError() is not empty %} {{ f.getError() }}{% endif %}<br />
    {% endif %}
  {{ f.render()|raw }}
  {% else %}
  {{ f.render()|raw }}
  {% endif %}<br />
  <br />
{% endfor %}