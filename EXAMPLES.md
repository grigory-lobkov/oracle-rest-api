### curl "http://w_action1:r1gn5@192.168.11.31/action/26477680" 
```
{data:[{"ID":26477680,"TYPE":1,"DATE_IN":"2018-03-01 14:25:25","RES_ID":null,"DEV_ID":24,"WEIGHT":0,"COMP_ID":null,"MAIN_DEV_ID":24}]}
```

### curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&fields=ID,TYPE" 
```
{data:[{"ID":26478395,"TYPE":1},{"ID":26478396,"TYPE":1},{"ID":26478398,"TYPE":1},{"ID":26478399,"TYPE":1},{"ID":26477680,"TYPE":1},{"ID":26478083,"TYPE":1},{"ID":26478091,"TYPE":1},{"ID":26478107,"TYPE":1},{"ID":26478134,"TYPE":1},{"ID":26478135,"TYPE":1},{"ID":26478011,"TYPE":1},{"ID":26478020,"TYPE":1},{"ID":26478026,"TYPE":1},{"ID":26478032,"TYPE":1},{"ID":26478036,"TYPE":1},{"ID":26478180,"TYPE":1},{"ID":26478282,"TYPE":1},{"ID":26478297,"TYPE":1},{"ID":26477387,"TYPE":1},{"ID":26478181,"TYPE":1},{"ID":26478281,"TYPE":1},{"ID":26478296,"TYPE":1}]}
```

### curl --data "{DEV_ID:24,WEIGHT:0,TYPE:1}" http://w_action1:r1gn5@192.168.11.31/action 
```
{rows:1}
```

### curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&limit=1&offset=2" 
```
{data:[{"ID":26478398,"TYPE":1,"DATE_IN":"2018-03-01 15:51:18","RES_ID":null,"DEV_ID":24,"WEIGHT":0,"COMP_ID":null,"MAIN_DEV_ID":24,"NUM9DHGIEHNVAK":3}]}
```

### curl "http://w_action1:r1gn5@192.168.11.31/action?DEV_ID=24&order=ID%20desc&fields=ID" 
```
{data:[{"ID":26478439},{"ID":26478399},{"ID":26478398},{"ID":26478396},{"ID":26478395},{"ID":26478297},{"ID":26478296},{"ID":26478282},{"ID":26478281},{"ID":26478181},{"ID":26478180},{"ID":26478135},{"ID":26478134},{"ID":26478107},{"ID":26478091},{"ID":26478083},{"ID":26478036},{"ID":26478032},{"ID":26478026},{"ID":26478020},{"ID":26478011},{"ID":26477680},{"ID":26477387}]}
```

