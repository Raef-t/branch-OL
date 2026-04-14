import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '/core/classes/selection_controller_class.dart';
import '/core/components/check_icon_component.dart';
import '/core/decorations/box_decorations.dart';

class CheckboxComponent extends StatelessWidget {
  const CheckboxComponent({super.key, required this.index});
  final int index;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Consumer<SelectionControllerClass>(
      builder: (context, controller, child) {
        if (!controller.selectionMode) return const SizedBox.shrink();
        bool isChecked = controller.isStudentSelected(index: index);
        return GestureDetector(
          onTap: () {
            controller.toggleStudent(index: index);
          },
          child: Container(
            height: size.height * (isRotait ? 0.018 : 0.04),
            width: size.width * 0.032,
            alignment: Alignment.center,
            decoration: BoxDecorations.boxDecorationToCheckboxComponent(
              context: context,
            ),
            child: isChecked ? const CheckIconComponent() : null,
          ),
        );
      },
    );
  }
}
