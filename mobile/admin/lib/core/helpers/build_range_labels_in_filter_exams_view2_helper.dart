import 'package:flutter/material.dart';

RangeLabels buildRangeLabelsInFilterExamsView2Helper({
  required RangeValues rangeValues,
}) {
  return RangeLabels(
    rangeValues.start.round().toString(),
    rangeValues.end.round().toString(),
  );
  //rangeValues.start it's mean From, rangeValues.end it's mean To, here give it left value and right value what are them
}
