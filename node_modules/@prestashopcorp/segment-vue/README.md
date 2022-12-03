# vue-segment

Vue plugin for Segment

> Vue.js plugin for Segment

[Segment Analytics.js Documentation](https://segment.com/docs/sources/website/analytics.js/)

## Requirements

Vue ^2.x or Vue ^3.x

```bash
npm install @prestashopcorp/segment-vue
```

### Vue 2

```js
import Vue from 'vue'
import SegmentVue from '@prestashopcorp/segment-vue'

Vue.use(SegmentVue, {
  id: 'XXXXX',
})
```

### Vue 3

```js
import { createApp } from 'vue'
import SegmentVue from '@prestashopcorp/segment-vue'

const app = createApp(App)

app.use(SegmentVue, {
  id: 'XXXXX',
})
```

## LoadOptions

For GDPR it's necessary to desactivate all integrations
An Example below :

```js
import Vue from 'vue'
import SegmentVue from '@prestashopcorp/segment-vue'

Vue.use(SegmentVue, {
  id: 'XXXXX',
  settings: {
    integrations: {
      All: false,
      'Segment.io': true,
    },
  },
})
```

### For example with axeptio

```js
axeptio.on('cookies:complete', function (choices) {
  if (choices.google_analytics && choices.hotjar) {
    window.analytics.load(SEGMENT_KEY, {
      integrations: {
        All: false,
        'Segment.io': true,
        'Google Analytics': true,
        Hotjar: true,
      },
    })
    window.location.reload()
  }
})
```

## Identify

add this on your layout file

```js
created(){
  this.$segment.identify({shopId}, {
    name: "FullName_account",
    email: "email_account",
    plan: "premium_account"
  })
}
```

## Track

add this on each method click

```js
handleClick(){
  this.$segment.track(NAME_YOUR_TRACK,
  //optional properties
  {
    name: "it's your track name",
    category: "ps_metrics",
  })
}
```

## Options

### route track option

put the name attribute in each route and add your router to the vue-segment initialization

### Vue 2

```js
export default {
  path: 'dashboard',
  name: 'dashboard', //Set name on each route
  component: DashboardApp,
}

Vue.use(SegmentVue, {
  id: 'XXXXX',
  router,
})
```

### Vue 3

```js
export default {
  path: 'dashboard',
  name: 'dashboard', //Set name on each route
  component: DashboardApp,
}

app.use(SegmentVue, {
  id: 'XXXXX',
  router,
})
```

### exclude route option

```js
export default {
  name: 'activity',
  path: 'activity',
  meta: { exclude: true }, // <= add this key in your route object, true to exclude, false to track
  component: () =>
    import(
      /* webpackChunkName: "dashboardActivity" */ '@/core/dashboard/pages/ActivityApp'
    ),
  redirect: 'activity/revenue',
  children: [RevenueRouter, OrderRouter, ConversionRouter, VisitRouter],
}
```

### page category option

put the name attribute in each route and add your router to the vue-segment initialization

### Vue 2

```js
Vue.use(SegmentVue, {
  id: 'XXXXX',
  router,
  pageCategory: 'ps_metrics_',
})
```

### Vue 3

```js
app.use(SegmentVue, {
  id: 'XXXXX',
  router,
  pageCategory: 'ps_metrics_',
})
```
