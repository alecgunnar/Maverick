{% for f in fields %}
  {% if f.getLabel() %}
    <strong>{{ f.getLabel() }}:</strong><br />
  {% endif %}
  {{ f.render()|raw }}<br />
  <br />
{% endfor %}