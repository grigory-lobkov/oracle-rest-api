## Deleting 
```
# curl -X DELETE http://w_all:ghjlern@192.168.11.31/action?DEV_ID=24
{rows:7}
```

# Inserting 
```
# curl --data "{DEV_ID:24,TYPE:1}" http://w_action1:r1gn5@192.168.11.31/action/
{rows:1}
```

```
# curl --data "{data:{DEV_ID:24,TYPE:2}}" http://w_action1:r1gn5@192.168.11.31/action
{rows:1}
```

```
# curl --data "{data:[{DEV_ID:24,TYPE:3},{DEV_ID:24,TYPE:3}]}" http://w_action1:r1gn5@192.168.11.31/action
{rows:2}
```

```
# curl --data "{names:[DEV_ID,"TYPE"],data:[[24,4],[24,5],[24,6]]}" http://w_action1:r1gn5@192.168.11.31/action
{rows:3}
```

```
# curl --data "{data:[]}" http://w_action1:r1gn5@192.168.11.31/action
{rows:0}
```

```
# curl --data "{data:{}}" http://w_action1:r1gn5@192.168.11.31/action
{rows:0}
```

```
# curl --data "{}" http://w_action1:r1gn5@192.168.11.31/action
{rows:0}
```

# Selecting 
```
# curl http://w_action1:r1gn5@192.168.11.31/action/20000
{data:[{"ID":20000,"TYPE":1,"DATE_IN":"2009-03-24 08:17:35","RES_ID":null,"DEV_ID":21,"WEIGHT":3.36,"COMP_ID":null,"MAIN_DEV_ID":21}]}
```

```
# curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&fields=ID,TYPE"
{data:[{"ID":26559235,"TYPE":4},{"ID":26559236,"TYPE":5},{"ID":26559237,"TYPE":6},{"ID":26559232,"TYPE":2},{"ID":26559230,"TYPE":1},{"ID":26559233,"TYPE":3},{"ID":26559234,"TYPE":3}]}
```

```
# curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&limit=1&offset=2"
{data:[{"ID":26559237,"TYPE":6,"DATE_IN":"2018-03-11 21:22:32","RES_ID":null,"DEV_ID":24,"WEIGHT":null,"COMP_ID":null,"MAIN_DEV_ID":24,"Z2DYQL7K":3}]}
```

```
# curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&order=ID%20desc&fields=ID"
{data:[{"ID":26559237},{"ID":26559236},{"ID":26559235},{"ID":26559234},{"ID":26559233},{"ID":26559232},{"ID":26559230}]}
```

## Updating 
```
# curl -X PUT --data "{TYPE:1}" "http://w_all:ghjlern@192.168.11.31/action?DEV_ID=24&TYPE=3"
{rows:2}
```

## Set field 
```
# curl -X PATH --data "1200" "http://w_all:ghjlern@192.168.11.31/action?DEV_ID=24&TYPE=2&fields=WEIGHT"
{rows:1,bytes:4}
```

## Get field 
```
# curl -X PATH "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&TYPE=2&fields=WEIGHT"
1200
```

## Result 
```
# curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&fields=ID,TYPE"
{data:[{"ID":26559235,"TYPE":4},{"ID":26559236,"TYPE":5},{"ID":26559237,"TYPE":6},{"ID":26559232,"TYPE":2},{"ID":26559230,"TYPE":1},{"ID":26559233,"TYPE":1},{"ID":26559234,"TYPE":1}]}
```

