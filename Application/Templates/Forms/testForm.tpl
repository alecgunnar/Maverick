{% for f in fields %}
  <div>
    {% if f is iterable %}
    <fieldset>
      <legend>{{ f.label }}</legend>
      {% for f in f.fields %}
        <div>
          {{ f.getLabel() }} {{ f.getError() }}<br />
          {{ f.getPrepended() }}{{ f.render()|raw }}{{ f.getAppended() }}
        </div>
      {% endfor %}
    </fieldset>
    {% else %}
    {{ f.getLabel() }} {{ f.getError() }}<br />
    {{ f.getPrepended() }}{{ f.render()|raw }}{{ f.getAppended() }}
    {% endif %}
  </div>
{% else %}
{{ form|raw }}
{% endfor %}

