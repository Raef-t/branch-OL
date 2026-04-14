import 'package:flutter/material.dart';
import '/core/classes/pie_chart_painter_class.dart';
import '/core/components/text_medium16_component.dart';
import '/gen/fonts.gen.dart';

class CustomTwoColorPieChartInCoursesDetailsView extends StatelessWidget {
  final int completedModules;
  final Color color;

  const CustomTwoColorPieChartInCoursesDetailsView({
    super.key,
    required this.completedModules,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SizedBox(
      height: size.height * (isRotait ? 0.058 : 0.09),
      width: size.width * (isRotait ? 0.098 : 0.07),
      child: CustomPaint(
        painter: PieChartPainterClass(
          completedModules: completedModules,
          color: color,
        ),
        child: Center(
          child: TextMedium16Component(
            text: '${((completedModules / 100) * 100).round()}',
            fontFamily: FontFamily.tajawal,
          ),
        ),
      ),
    );
  }
}
