# Rate Limiting Strategies POC

This POC contains demos for the following strategies:

1. [*Variable* Fixed Window](#variable-fixed-window)
2. [Sliding Window](#sliding-window)
3. [Token Bucket](#token-bucket)
4. [Compound](#compound)

## *Variable* Fixed Window

Variation of the standard *Fixed Window* strategy but starts the window on the *first* request (instead of a
static time like *top of the hour*).

### Example 1: Full Saturation

Hit the rate limiter as fast as possible right away:

```
Window: limit=5; duration=10s

Saturate limiter...
11:39:35			remaining=4; reset=10; limit=5
11:39:35	(burst)		remaining=3; reset=10; limit=5
11:39:35	(burst)		remaining=2; reset=10; limit=5
11:39:35	(burst)		remaining=1; reset=10; limit=5
11:39:35	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
11:39:45			remaining=4; reset=10; limit=5
11:39:45	(burst)		remaining=3; reset=10; limit=5
11:39:45	(burst)		remaining=2; reset=10; limit=5
11:39:45	(burst)		remaining=1; reset=10; limit=5
11:39:45	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
11:39:55			remaining=4; reset=10; limit=5
11:39:55	(burst)		remaining=3; reset=10; limit=5
11:39:55	(burst)		remaining=2; reset=10; limit=5
11:39:55	(burst)		remaining=1; reset=10; limit=5
11:39:55	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
...
```

### Example 2: Stagger then Saturate

For the first window, stagger the hits, then fully saturate:

```
Window: limit=5; duration=10s

Staggering hits in first window...
11:41:20			remaining=4; reset=10; limit=5
11:41:21			remaining=3; reset=9; limit=5
11:41:22			remaining=2; reset=8; limit=5
11:41:23			remaining=1; reset=7; limit=5
Saturate limiter...
11:41:24			remaining=0; reset=6; limit=5
Rate limit exceeded, waiting 6 seconds...
11:41:30			remaining=4; reset=10; limit=5
11:41:30	(burst)		remaining=3; reset=10; limit=5
11:41:30	(burst)		remaining=2; reset=10; limit=5
11:41:30	(burst)		remaining=1; reset=10; limit=5
11:41:30	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
11:41:40			remaining=4; reset=10; limit=5
11:41:40	(burst)		remaining=3; reset=10; limit=5
11:41:40	(burst)		remaining=2; reset=10; limit=5
11:41:40	(burst)		remaining=1; reset=10; limit=5
11:41:40	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
...
```

## Sliding Window

### Example 1: Full Saturation

Hit the rate limiter as fast as possible right away:

```
Window: limit=5; duration=10s

Saturate limiter...
11:42:54			remaining=4; reset=10; limit=5
11:42:54	(burst)		remaining=3; reset=10; limit=5
11:42:54	(burst)		remaining=2; reset=10; limit=5
11:42:54	(burst)		remaining=1; reset=10; limit=5
11:42:54	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
11:43:04			remaining=4; reset=10; limit=5
11:43:04	(burst)		remaining=3; reset=10; limit=5
11:43:04	(burst)		remaining=2; reset=10; limit=5
11:43:04	(burst)		remaining=1; reset=10; limit=5
11:43:04	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
11:43:14			remaining=4; reset=10; limit=5
11:43:14	(burst)		remaining=3; reset=10; limit=5
11:43:14	(burst)		remaining=2; reset=10; limit=5
11:43:14	(burst)		remaining=1; reset=10; limit=5
11:43:14	(burst)		remaining=0; reset=10; limit=5
Rate limit exceeded, waiting 10 seconds...
...
```

Note: For the same window definition, this behaves identical to Fixed Window

### Example 2: Stagger then Saturate

For the first window, stagger the hits, then fully saturate:

```
Window: limit=5; duration=10s

Staggering hits in first window...
11:44:59			remaining=4; reset=10; limit=5
11:45:00			remaining=3; reset=9; limit=5
11:45:01			remaining=2; reset=8; limit=5
11:45:02			remaining=1; reset=7; limit=5
Saturate limiter...
11:45:03			remaining=0; reset=6; limit=5
Rate limit exceeded, waiting 6 seconds...
11:45:09			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:10			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:11			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:12			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:13			remaining=0; reset=6; limit=5
Rate limit exceeded, waiting 6 seconds...
11:45:19			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:20			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:21			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:22			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:45:23			remaining=0; reset=6; limit=5
Rate limit exceeded, waiting 6 seconds...
...
```

## Token Bucket

### Example 1: Full Saturation

Hit the rate limiter as fast as possible right away:

```
Bucket: burst=5; fill-rate=1/s

Saturate limiter...
11:46:06			remaining=4; reset=1; limit=5
11:46:06	(burst)		remaining=3; reset=2; limit=5
11:46:06	(burst)		remaining=2; reset=3; limit=5
11:46:06	(burst)		remaining=1; reset=4; limit=5
11:46:06	(burst)		remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:07			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:08			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:09			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:10			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:11			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:12			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:13			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:46:14			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
```

### Example 2: Stagger then Saturate

For the first window, stagger the hits, then fully saturate:

```
Bucket: burst=5; fill-rate=1/s

Staggering first hits...
11:47:57			remaining=4; reset=1; limit=5
11:47:58			remaining=4; reset=1; limit=5
11:47:59			remaining=4; reset=1; limit=5
11:48:00			remaining=4; reset=1; limit=5
Saturate limiter...
11:48:01			remaining=4; reset=1; limit=5
11:48:01	(burst)		remaining=3; reset=2; limit=5
11:48:01	(burst)		remaining=2; reset=3; limit=5
11:48:01	(burst)		remaining=1; reset=4; limit=5
11:48:01	(burst)		remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:48:03			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:48:04			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:48:05			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:48:06			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:48:07			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:48:08			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
11:48:09			remaining=0; reset=1; limit=5
Rate limit exceeded, waiting 1 seconds...
```

## Compound

### Example 1: 3 Fixed Windows, 1 hit per second

```
Window 1: limit=4; duration=1s
Window 2: limit=8; duration=10s
Window 3: limit=12; duration=30s

Hit limiter 1/s...
11:49:27			remaining=3; reset=1; limit=4		(the closest limit to reach is "Window 1")
11:49:28			remaining=3; reset=1; limit=4
11:49:29			remaining=3; reset=1; limit=4
11:49:30			remaining=3; reset=1; limit=4
11:49:31			remaining=3; reset=1; limit=4
11:49:32			remaining=2; reset=5; limit=8		(the closest limit to reach is now "Window 2")
11:49:33			remaining=1; reset=4; limit=8
11:49:34			remaining=0; reset=3; limit=8
11:49:35	(exceeded)	remaining=0; reset=2; limit=8
11:49:36	(exceeded)	remaining=0; reset=1; limit=8
11:49:37			remaining=1; reset=20; limit=12		(the closest limit to reach is now "Window 3")
11:49:38			remaining=0; reset=19; limit=12
11:49:39	(exceeded)	remaining=0; reset=18; limit=12
11:49:40	(exceeded)	remaining=0; reset=17; limit=12
11:49:41	(exceeded)	remaining=0; reset=16; limit=12
11:49:42	(exceeded)	remaining=0; reset=15; limit=12
11:49:43	(exceeded)	remaining=0; reset=14; limit=12
11:49:44	(exceeded)	remaining=0; reset=13; limit=12
11:49:45	(exceeded)	remaining=0; reset=12; limit=12
11:49:46	(exceeded)	remaining=0; reset=11; limit=12
11:49:47	(exceeded)	remaining=0; reset=10; limit=12
11:49:48	(exceeded)	remaining=0; reset=9; limit=12
11:49:49	(exceeded)	remaining=0; reset=8; limit=12
11:49:50	(exceeded)	remaining=0; reset=7; limit=12
11:49:51	(exceeded)	remaining=0; reset=6; limit=12
11:49:52	(exceeded)	remaining=0; reset=5; limit=12
11:49:53	(exceeded)	remaining=0; reset=4; limit=12
11:49:54	(exceeded)	remaining=0; reset=3; limit=12
11:49:55	(exceeded)	remaining=0; reset=2; limit=8
11:49:56	(exceeded)	remaining=0; reset=1; limit=8
11:49:57			remaining=3; reset=1; limit=4		(the closest limit to reach is now "Window 1")
11:49:58			remaining=3; reset=1; limit=4
11:49:59			remaining=3; reset=1; limit=4
11:50:00			remaining=3; reset=1; limit=4
11:50:01			remaining=3; reset=1; limit=4
11:50:02			remaining=2; reset=5; limit=8
11:50:03			remaining=1; reset=4; limit=8
11:50:04			remaining=0; reset=3; limit=8
11:50:05	(exceeded)	remaining=0; reset=2; limit=8
11:50:06	(exceeded)	remaining=0; reset=1; limit=8
11:50:07			remaining=1; reset=20; limit=12
11:50:08			remaining=0; reset=19; limit=12
11:50:09	(exceeded)	remaining=0; reset=18; limit=12
```
