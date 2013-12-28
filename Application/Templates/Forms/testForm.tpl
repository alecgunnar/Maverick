{% macro renderFormField(field) %}
{% if field.getPrepended() is not empty %}{{ field.getPrepended()|raw }} {% endif %}{{ field.render()|raw }}{% if field.getAppended() is not empty %} {{ field.getAppended()|raw}}{% endif %}
{% endmacro %}
{% macro renderAttachedFields(fields) %}
{% import _self as form %}
{% for f in fields %}
{{ form.renderFormField(f) }}
{% endfor %}
{% endmacro %}
{% macro renderField(field) %}
{% import _self as form %}
<div>
  {% if field.getLabel() is not empty %}<label>{{ field.getLabel() }}</label>{% endif %}{% if field.getError() %}{% if field.getLabel() is not empty %} &middot; {% endif %}{{ field.getError()|raw }}{% endif %}{% if field.getLabel() or field.getError() %}<br />{% endif %}
  {{ form.renderFormField(field) }}{% if field.getAttachedFields()|length %}{{ form.renderAttachedFields(field.getAttachedFields()) }}{% endif %}
  {% if field.getDescription() is not empty %}
  <div>
    {{ field.getDescription() }}
  </div>
  {% endif %}
</div>
{% endmacro %}
{% macro renderFields(fields) %}
  {% import _self as form %}
  
  {% for f in fields %}
    {% if f is iterable %}
      <fieldset>
        {% if f.label is not empty %}<legend>{{ f.label }}</legend>{% endif %}
        {{ form.renderFields(f.fields) }}
      </fieldset>
    {% else %}
      {{ form.renderField(f) }}
    {% endif %}
  {% endfor %}
{% endmacro %}

{% import _self as form %}

{{ form.renderFields(fields) }}