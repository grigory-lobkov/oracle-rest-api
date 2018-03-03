### curl --data "{DEV_ID:24,TYPE:1}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:1}
```

### curl --data "{data:{DEV_ID:24,TYPE:2}}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:1}
```

### curl --data "{data:[{DEV_ID:24,TYPE:3},{DEV_ID:24,TYPE:3}]}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:2}
```

### curl --data "{names:[DEV_ID,"TYPE"],data:[[24,4],[24,5],[24,6]]}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:3}
```

### curl --data "{data:[]}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:0}
```

### curl --data "{data:{}}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:0}
```

### curl --data "{}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:0}
```

### curl "http://w_action1:r1gn5@192.168.11.31/action/26477680" 
```
{data:[{"ID":26477680,"TYPE":1,"DATE_IN":"2018-03-01 14:25:25","RES_ID":null,"DEV_ID":24,"WEIGHT":0,"COMP_ID":null,"MAIN_DEV_ID":24}]}
```

### curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&fields=ID,TYPE" 
```
{data:[{"ID":26478395,"TYPE":1},{"ID":26478396,"TYPE":1},{"ID":26478398,"TYPE":1},{"ID":26478399,"TYPE":1},{"ID":26477680,"TYPE":1},{"ID":26478083,"TYPE":1},{"ID":26478091,"TYPE":1},{"ID":26478107,"TYPE":1},{"ID":26478134,"TYPE":1},{"ID":26478135,"TYPE":1},{"ID":26478011,"TYPE":1},{"ID":26478020,"TYPE":1},{"ID":26478026,"TYPE":1},{"ID":26478032,"TYPE":1},{"ID":26478036,"TYPE":1},{"ID":26478180,"TYPE":1},{"ID":26478282,"TYPE":1},{"ID":26478297,"TYPE":1},{"ID":26478439,"TYPE":1},{"ID":26501667,"TYPE":2},{"ID":26491969,"TYPE":1},{"ID":26501666,"TYPE":1},{"ID":26501493,"TYPE":1},{"ID":26501494,"TYPE":2},{"ID":26501503,"TYPE":3},{"ID":26501508,"TYPE":3},{"ID":26501509,"TYPE":3},{"ID":26501608,"TYPE":4},{"ID":26501609,"TYPE":5},{"ID":26501610,"TYPE":6},{"ID":26501611,"TYPE":3},{"ID":26501612,"TYPE":3},{"ID":26501613,"TYPE":2},{"ID":26501614,"TYPE":1},{"ID":26501640,"TYPE":3},{"ID":26501641,"TYPE":3},{"ID":26501668,"TYPE":3},{"ID":26501669,"TYPE":3},{"ID":26501670,"TYPE":4},{"ID":26501671,"TYPE":5},{"ID":26501672,"TYPE":6},{"ID":26477387,"TYPE":1},{"ID":26478181,"TYPE":1},{"ID":26478281,"TYPE":1},{"ID":26478296,"TYPE":1}]}
```

### curl --data "{DEV_ID:24,WEIGHT:0,TYPE:1}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:1}
```

### curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&limit=1&offset=2" 
```
{data:[{"ID":26478398,"TYPE":1,"DATE_IN":"2018-03-01 15:51:18","RES_ID":null,"DEV_ID":24,"WEIGHT":0,"COMP_ID":null,"MAIN_DEV_ID":24,"Z2DYQL7K":3}]}
```

### curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&order=ID%20desc&fields=ID" 
```
{data:[{"ID":26501673},{"ID":26501672},{"ID":26501671},{"ID":26501670},{"ID":26501669},{"ID":26501668},{"ID":26501667},{"ID":26501666},{"ID":26501641},{"ID":26501640},{"ID":26501614},{"ID":26501613},{"ID":26501612},{"ID":26501611},{"ID":26501610},{"ID":26501609},{"ID":26501608},{"ID":26501509},{"ID":26501508},{"ID":26501503},{"ID":26501494},{"ID":26501493},{"ID":26491969},{"ID":26478439},{"ID":26478399},{"ID":26478398},{"ID":26478396},{"ID":26478395},{"ID":26478297},{"ID":26478296},{"ID":26478282},{"ID":26478281},{"ID":26478181},{"ID":26478180},{"ID":26478135},{"ID":26478134},{"ID":26478107},{"ID":26478091},{"ID":26478083},{"ID":26478036},{"ID":26478032},{"ID":26478026},{"ID":26478020},{"ID":26478011},{"ID":26477680},{"ID":26477387}]}
```

