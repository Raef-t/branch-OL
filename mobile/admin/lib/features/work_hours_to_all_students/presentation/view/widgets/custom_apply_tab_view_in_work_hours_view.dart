import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_card_in_apply_tab_view_in_work_hours_view.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_column_times_with_divider_in_apply_tab_view_in_work_hours_view.dart';

class CustomApplyTabViewInWorkHoursView extends StatelessWidget {
  const CustomApplyTabViewInWorkHoursView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Stack(
      children: [
        const CustomColumnTimesWithDividerInApplyTabViewInWorkHoursView(),
        Positioned(
          left: size.width * 0.172,
          child: CustomCardInApplyTabViewInWorkHoursView(
            percentHeight: isRotait ? 0.2 : 0.285,
            color: ColorsStyle.veryLittlePinkColor2,
          ),
        ),
        Positioned(
          left: size.width * 0.42,
          top: size.height * (isRotait ? 0.193 : 0.3),
          child: CustomCardInApplyTabViewInWorkHoursView(
            percentHeight: isRotait ? 0.11 : 0.14,
            color: ColorsStyle.veryLittleGreenColor,
          ),
        ),
        Positioned(
          left: size.width * 0.63,
          top: size.height * (isRotait ? 0.298 : 0.45),
          child: CustomCardInApplyTabViewInWorkHoursView(
            percentHeight: isRotait ? 0.11 : 0.14,
            color: ColorsStyle.veryLittlePinkColor,
          ),
        ),
        Positioned(
          left: size.width * 0.2,
          top: size.height * (isRotait ? 0.405 : 0.6),
          child: CustomCardInApplyTabViewInWorkHoursView(
            percentHeight: isRotait ? 0.11 : 0.14,
            color: ColorsStyle.veryLittleOrangeColor,
          ),
        ),
        Positioned(
          left: size.width * 0.8,
          top: size.height * (isRotait ? 0.508 : 0.755),
          child: CustomCardInApplyTabViewInWorkHoursView(
            percentHeight: isRotait ? 0.11 : 0.14,
            color: ColorsStyle.veryLittleSkyBlueColor,
          ),
        ),
      ],
    );
  }
}
