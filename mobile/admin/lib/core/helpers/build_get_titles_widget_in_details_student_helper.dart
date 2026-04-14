import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/components/text_normal10_component.dart';
import '/core/lists/months_to_rating_card_in_details_student_view_list.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

Widget Function(double value, TitleMeta meta)
buildgetTitlesWidgetInDetailsStudentHelper({required BuildContext context}) {
  return (value, meta) {
    return OnlyPaddingWithChild.top6(
      context: context,
      child: TextNormal10Component(
        text: monthsToRatingCardInDetailsStudentViewList[value.toInt()],
        fontFamily: FontFamily.poppins,
        color: ColorsStyle.blackColor,
      ),
    );
  };
}
