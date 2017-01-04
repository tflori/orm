---
layout: default
title: Working With Relations
permalink: /relations.html
---
## Working With Relations

Relations can be used for joins in EntityFetcher and of course to fetch related objects from Entity. In this doc we
want to describe how and what else you can do with these relations. How to add relations and how to remove relations.

### Fetch relations

You can fetch relations with fetch but also with the default getter. For a relation with cardinality one you will 
always receive the Entity (or null). But for relations with cardinality with many you will receive an array from getter
and a `RelationFetcher` from fetch.

The getter will only execute a query when it is not fetched previously - and for the owner try the mapping first. Fetch
will always execute a query to receive the current data. This also can be used to update the relation.
