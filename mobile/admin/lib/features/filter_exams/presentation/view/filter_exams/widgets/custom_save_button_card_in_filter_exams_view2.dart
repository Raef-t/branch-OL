import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_contain_save_button_card_in_filter_exams_view2.dart';

class CustomSaveButtonCardInFilterExamsView2 extends StatelessWidget {
  const CustomSaveButtonCardInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return OnlyPaddingWithChild.left38(
      context: context,
      child: Align(
        alignment: Alignment.centerLeft,
        child: Container(
          width: size.width * (isRotait ? 0.212 : 0.18),
          height: size.height * (isRotait ? 0.05 : 0.08),
          alignment: Alignment.center,
          decoration:
              BoxDecorations.boxDecorationToSaveButtonCardInFilterExamsView2(
                context: context,
              ),
          child: const CustomContainSaveButtonCardInFilterExamsView2(),
        ),
      ),
    );
  }
}
