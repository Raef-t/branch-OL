import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomThemeWidgetToRangeSliderInFilterExamsView2 extends StatelessWidget {
  const CustomThemeWidgetToRangeSliderInFilterExamsView2({
    super.key,
    required this.child,
  });
  final Widget child;
  @override
  Widget build(BuildContext context) {
    return Theme(
      //Theme Widget to change theme all things(color, style,..)
      data: Theme.of(context).copyWith(
        sliderTheme: SliderTheme.of(context).copyWith(
          valueIndicatorColor: ColorsStyle.littleVinicColor,
          //this color for card that appear when you walk
          valueIndicatorTextStyle: TextsStyle.medium12(context: context)
              .copyWith(
                color: ColorsStyle.littleGreyColor,
                fontFamily: FontFamily.tajawal,
              ), //this style for card that appear when you walk
          activeTrackColor: ColorsStyle.littleVinicColor2,
          //color to line that between to dots
          thumbColor: ColorsStyle.littleVinicColor2,
          //color to dots
        ),
      ),
      child: child,
    );
  }
}
