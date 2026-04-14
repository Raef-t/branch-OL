import 'package:flutter/material.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/decorations/box_decorations.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';

class CustomNotificationCardInDetailsStudentView extends StatelessWidget {
  const CustomNotificationCardInDetailsStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return GestureDetector(
      onTap: () =>
          pushGoRouterHelper(context: context, view: kNotificationsViewRouter),
      child: Container(
        margin: OnlyPaddingWithoutChild.left30(context: context),
        height: size.height * (isRotait ? 0.055 : 0.075),
        width: size.width * (isRotait ? 0.094 : 0.07),
        decoration:
            BoxDecorations.boxDecorationToNotificationCardInDetailsStudentView(
              context: context,
            ),
      ),
    );
  }
}
