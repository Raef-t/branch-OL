import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '/core/classes/selection_controller_class.dart';
import '/core/components/text_medium14_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/class/presentation/view/widgets/custom_checkbox_select_all_in_class_view.dart';

class CustomTextWithCheckboxSelectAllInClassView extends StatelessWidget {
  const CustomTextWithCheckboxSelectAllInClassView({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<SelectionControllerClass>(
      builder: (context, controller, child) {
        if (!controller.selectionMode) return const SizedBox.shrink();
        return OnlyPaddingWithChild.left30(
          context: context,
          child: Row(
            children: [
              const TextMedium14Component(
                text: 'تحديد الكل',
                color: ColorsStyle.mediumBlackColor2,
              ),
              Widths.width7(context: context),
              CustomCheckboxSelectAllInClassView(controller: controller),
            ],
          ),
        );
      },
    );
  }
}
