import { serializeType } from '../common';
import { TYPE } from '../constants';
export function serializeUndefined(val) {
  return serializeType(TYPE.UNDEFINED, val);
}
export function deserializeUndefined() {// pass
}