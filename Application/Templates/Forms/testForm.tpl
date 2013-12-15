{% for f in fields %}
  <div>
    {% if f is iterable %}
    <fieldset>
      <legend>{{ f.label }}</legend>
      {% for f in f.fields %}
        <div>
          {{ f.getLabel() }} {{ f.getError() }}<br />
          {{ f.render()|raw }}
        </div>
      {% endfor %}
    </fieldset>
    {% else %}
    {{ f.getLabel() }} {{ f.getError() }}<br />
    {{ f.render()|raw }}
    {% endif %}
  </div>
{% else %}
{{ form|raw }}
{% endfor %}

